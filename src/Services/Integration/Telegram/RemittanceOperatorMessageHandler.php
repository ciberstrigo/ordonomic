<?php

namespace Jegulnomic\Services\Integration\Telegram;

use DI\Attribute\Inject;
use Jegulnomic\Entity\Withdrawal;
use Jegulnomic\Services\WithdrawalOperations;
use Jegulnomic\Systems\Authenticator;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;

class RemittanceOperatorMessageHandler
{
    private array $update;

    public function __construct(
        #[Inject(TelegramIntegration::class)]
        readonly private TelegramIntegration $telegram,
        #[Inject(WithdrawalOperations::class)]
        readonly private WithdrawalOperations $withdrawalOperations,
        #[Inject(DatabaseStorage::class)]
        readonly private StorageInterface $storage,
    ) {
    }

    public function setUpdate(array $update): self
    {
        $this->update = $update;

        return $this;
    }

    public function start()
    {
        $id = $this->update['message']['from']['id'];
        $operator = Authenticator::getRemittanceOperator($id);

        if (!$operator) {
            $this->telegram->sendMessage([
                'chat_id' => $this->update['message']['from']['id'],
                'text' => 'Вас нет в списке операторов. Пройдите регистрацию. '
                    . Authenticator::getRegistrationLink($id)
            ]);
            return;
        }

        if (!$operator->isVerified) {
            $this->telegram->sendMessage([
                'chat_id' => $id,
                'text' => 'Ваш аккаунт не верифицирован. Ожидайте верификацию администратора.'
            ]);
            return;
        }

        if (!$operator->isAllowToProceed()) {
            $this->telegram->sendMessage([
                'chat_id' => $id,
                'text' => 'Пожалуйста, войдите в систему чтобы продолжать получать уведомления. '
                    . Authenticator::getLoginLink($this->update['message']['from']['id'])
            ]);
            return;
        }

        Authenticator::updateSession($id);

        $this->telegram->sendMessage([
            'chat_id' => $id,
            'text' => 'Вы находитесь в системе. Ваша сессия обновлена и действительна до: '
                . date("d F Y H:i:s", $operator->sessionUntil)
        ]);
    }

    public function debug()
    {
        $this->telegram->sendMessage([
            'chat_id' => $this->update['message']['from']['id'],
            'text' => json_encode($this->update, JSON_UNESCAPED_UNICODE + JSON_PRETTY_PRINT),
        ]);
    }

    public function callbackQuery()
    {
        $this->iPaidButtonClickedScenario();
    }

    private function iPaidButtonClickedScenario()
    {
        $callbackDataRaw = $this->update['callback_query']['data'];
        $callbackData = explode(':', $callbackDataRaw);

        $withdrawalId = $callbackData[0] ?? '';
        $withdrawalAction = $callbackData[1] ?? '';

        if (empty($withdrawalId) || empty($withdrawalAction)) {
            return;
        }

        if (!method_exists(WithdrawalOperations::class, $withdrawalAction)) {
            return;
        }

        $withdrawal = $this->storage->getOne(
            Withdrawal::class,
            'WHERE id = :id',
            [':id' => $withdrawalId]
        );

        if (null === $withdrawal) {
            return;
        }

        $response = $this->withdrawalOperations->$withdrawalAction($withdrawal);

        $r = $this->telegram->editMessageText([
            'chat_id' => $this->update['callback_query']['message']['chat']['id'],
            'message_id' => $this->update['callback_query']['message']['message_id'],
            'text' => $this->update['callback_query']['message']['text'] . "\n\n" . $response,
            'reply_markup' => json_encode(['inline_keyboard' => [[]]]),
        ]);
    }
}
