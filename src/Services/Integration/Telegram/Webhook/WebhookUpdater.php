<?php

namespace Jegulnomic\Services\Integration\Telegram\Webhook;

use DI\Attribute\Inject;
use http\Exception\RuntimeException;
use Jegulnomic\Controller\Logger\Callback\TelegramBotCallback as LoggerCallback;
use Jegulnomic\Controller\RemittanceOperator\Callback\TelegramBotCallback as OperatorCallback;
use Jegulnomic\Services\Integration\Telegram\AbstractBotProvider;
use Jegulnomic\Services\Integration\Telegram\Logger\BotProvider as LoggerBotProvider;
use Jegulnomic\Services\Integration\Telegram\RemittanceOperator\BotProvider as OperatorBotProvider;
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

    private function update(
        AbstractBotProvider $botProvider,
        string $callbackClass
    ): void {
        $url = $this->publicUrlProvider->getControllerUrl(
            $callbackClass
        );

        $isSet = $botProvider->getBot()->setWebhook($url);

        if (!$isSet) {
            throw new RuntimeException('Webhook was not set.');
        }
    }
}