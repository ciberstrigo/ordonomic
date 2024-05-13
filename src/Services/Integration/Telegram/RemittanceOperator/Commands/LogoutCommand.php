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

class LogoutCommand extends Command
{
    protected string $command = 'logout';

    protected ?string $description = 'Logout from system.';

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

        if ($operator && $operator->isAllowToProceed()) {
            $this->authenticator->logout($operator);
            $bot->sendMessage('Вы вышли из системы. Чтобы снова начат получать уведомления напишите /start');
            return;
        }

        $bot->sendMessage('Команда не найдена.');
    }
}
