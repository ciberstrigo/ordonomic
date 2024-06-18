<?php

namespace Jegulnomic\Services\Integration\Telegram\Webhook;

use DI\Attribute\Inject;
use Jegulnomic\Controller\Logger\Callback\TelegramBotCallback as LoggerCallback;
use Jegulnomic\Controller\RemittanceOperator\Callback\TelegramBotCallback as OperatorCallback;
use Jegulnomic\Controller\Customer\Callback\TelegramBotCallback as CustomerCallback;
use Jegulnomic\Services\Integration\Telegram\AbstractBotProvider;
use Jegulnomic\Services\Integration\Telegram\Logger\BotProvider as LoggerBotProvider;
use Jegulnomic\Services\Integration\Telegram\RemittanceOperator\BotProvider as OperatorBotProvider;
use Jegulnomic\Services\Integration\Telegram\Customer\BotProvider as CustomerBotProvider;
use Jegulnomic\Systems\PublicUrlProvider;

readonly class WebhookUpdater
{
    public function __construct(
        #[Inject(PublicUrlProvider::class)]
        private PublicUrlProvider $publicUrlProvider,
        #[Inject(OperatorBotProvider::class)]
        private AbstractBotProvider $operatorBotProvider,
        #[Inject(LoggerBotProvider::class)]
        private AbstractBotProvider $loggerBotProvider,
        #[Inject(CustomerBotProvider::class)]
        private CustomerBotProvider $customerBotProvider
    ) {
    }

    public function forRemittanceOperator(): void
    {
        $this->update(
            $this->operatorBotProvider,
            OperatorCallback::class
        );
    }

    public function forLogger(): void
    {
        $this->update(
            $this->loggerBotProvider,
            LoggerCallback::class
        );
    }

    public function forCustomer(): void
    {
        $this->update(
            $this->customerBotProvider,
            CustomerCallback::class
        );
    }

    private function update(
        AbstractBotProvider $botProvider,
        string $callbackClass
    ): void {
        $url = $this->publicUrlProvider->getControllerUrl(
            $callbackClass
        );

        $isSet = $botProvider->getBot()->setWebhook($url);

        if (!$isSet) {
            throw new \RuntimeException('Webhook updating failed. ' . $url);
        }
    }
}
