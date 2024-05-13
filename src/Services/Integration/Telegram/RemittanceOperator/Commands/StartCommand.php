<?php

namespace Jegulnomic\Services\Integration\Telegram\RemittanceOperator\Commands;

use DI\Attribute\Inject;
use Jegulnomic\Services\Authenticator\RemittanceOperatorAuthenticator;
use Jegulnomic\Systems\BaseAuthenticator;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;
use SergiX44\Nutgram\Telegram\Types\WebApp\WebAppInfo;

class StartCommand extends Command
{
    protected string $command = 'start';

    protected ?string $description = 'Start using our application. Login or pass the registration though this command.';

    protected BaseAuthenticator $authenticator;

    public function setAuthenticator(BaseAuthenticator $authenticator): self
    {
        $this->authenticator = $authenticator;

        return $this;
    }

    public function handle(Nutgram $bot): void
    {
        $id = $bot->userId();
        $operator = $this->authenticator->getUser($id);

        if (!$operator) {
            $bot->sendMessage(
                'Вас нет в списке операторов. Пройдите регистрацию. ',
                reply_markup: InlineKeyboardMarkup::make()
                    ->addRow(
                        InlineKeyboardButton::make(
                            'Регистрация',
                            web_app: WebAppInfo::make($this->authenticator->getRegistrationLink($id))
                        )
                    )
            );
            return;

        }

        if (!$operator->isVerified) {
            $bot->sendMessage('Ваш аккаунт не верифицирован. Ожидайте верификацию администратора.');
            return;
        }

        if (!$operator->isAllowToProceed()) {
            $bot->sendMessage(
                'Пожалуйста, войдите в систему чтобы продолжать получать уведомления. ',
                reply_markup: InlineKeyboardMarkup::make()
                    ->addRow(
                        InlineKeyboardButton::make(
                            'Вход',
                            web_app: WebAppInfo::make($this->authenticator->getLoginLink($id))
                        )
                    )
            );
            return;
        }
        $this->authenticator->updateSession($id);

        $bot->sendMessage('Вы находитесь в системе. Ваша сессия обновлена и действительна до: '
                . date("d F Y H:i:s", $operator->sessionUntil));
    }
}