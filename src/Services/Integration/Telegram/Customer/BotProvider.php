<?php

namespace Jegulnomic\Services\Integration\Telegram\Customer;

use DI\Attribute\Inject;
use Jegulnomic\Services\Authenticator\CustomerAuthenticator;
use Jegulnomic\Services\Integration\Telegram\AbstractBotProvider;
use Jegulnomic\Services\Integration\Telegram\Customer\Commands\LogoutCommand;
use Jegulnomic\Services\Integration\Telegram\Customer\Commands\StartCommand;
use Jegulnomic\Systems\BaseAuthenticator;
use Override;
use SergiX44\Nutgram\Nutgram;

class BotProvider extends AbstractBotProvider
{
    public function __construct(
        #[Inject(CustomerAuthenticator::class)]
        private readonly BaseAuthenticator $authenticator
    ) {
    }

    #[Override]
    protected function getToken(): string
    {
        return $_ENV['TELEGRAM_CUSTOMER_BOT_TOKEN'];
    }

    #[Override]
    protected function setUpBehaviour(Nutgram $client): void
    {
        $client->registerCommand((new StartCommand())->setAuthenticator($this->authenticator));
        $client->registerCommand((new LogoutCommand())->setAuthenticator($this->authenticator));
    }
}
