<?php

namespace Jegulnomic\Command\Telegram;

use DI\Attribute\Inject;
use Jegulnomic\Command\AbstractCommand;
use Jegulnomic\Controller\RemittanceOperator\Callback\TelegramBotCallback as OperatorCallback;
use Jegulnomic\Controller\Logger\Callback\TelegramBotCallback as LoggerCallback;
use Jegulnomic\Services\Integration\Telegram\AbstractBotProvider;
use Jegulnomic\Services\Integration\Telegram\RemittanceOperator\BotProvider as OperatorBotProvider;
use Jegulnomic\Services\Integration\Telegram\Logger\BotProvider as LoggerBotProvider;
use Jegulnomic\Systems\Command;
use Jegulnomic\Systems\PublicUrlProvider;

readonly class UpdateWebhook extends AbstractCommand
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

        Command::output('Updating webhook to: ' . $url);

        $isSet = $botProvider->getBot()->setWebhook($url);

        if ($isSet) {
            Command::output('Webhook updated');
            return;
        }

        Command::output('Webhook updating failure');
    }
}
