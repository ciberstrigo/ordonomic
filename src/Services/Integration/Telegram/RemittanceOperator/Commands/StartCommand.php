<?php

namespace Jegulnomic\Services\Integration\Telegram\RemittanceOperator\Commands;

use Jegulnomic\Systems\Authenticator;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Nutgram;

class StartCommand extends Command
{
    protected string $command = 'start';

    protected ?string $description = 'Start using our application. Login or pass the registration though this command.';

    public function handle(Nutgram $bot): void
    {
        $id = $bot->userId();
        $operator = Authenticator::getRemittanceOperator($id);

        if (!$operator) {
            $bot->sendMessage('Вас нет в списке операторов. Пройдите регистрацию. '
                    . Authenticator::getRegistrationLink($id));
            return;
        }

        if (!$operator->isVerified) {
            $bot->sendMessage('Ваш аккаунт не верифицирован. Ожидайте верификацию администратора.');
            return;
        }

        if (!$operator->isAllowToProceed()) {
            $bot->sendMessage('Пожалуйста, войдите в систему чтобы продолжать получать уведомления. '
                    . Authenticator::getLoginLink($id)
            );
            return;
        }

        Authenticator::updateSession($id);

        $bot->sendMessage('Вы находитесь в системе. Ваша сессия обновлена и действительна до: '
                . date("d F Y H:i:s", $operator->sessionUntil));
    }
}