<?php

namespace Jegulnomic\Command\Telegram;

use DI\Attribute\Inject;
use Jegulnomic\Command\AbstractCommand;
use Jegulnomic\Controller\RemittanceOperator\Callback\TelegramBotCallback;
use Jegulnomic\Services\Integration\Telegram\RemittanceOperator\BotProvider;
use Jegulnomic\Systems\Command;
use Jegulnomic\Systems\PublicUrlProvider;

readonly class UpdateWebhook extends AbstractCommand
{
    public function __construct(
        #[Inject(PublicUrlProvider::class)]
        private PublicUrlProvider $publicUrlProvider,
        #[Inject(BotProvider::class)]
        private BotProvider $botProvider
    ) {
    }

    public function forRemittanceOperator()
    {
        $url = $this->publicUrlProvider->getTelegramWebhookUrl(
            TelegramBotCallback::class
        );

        Command::output('Updating webhook to: ' . $url);

        $isSet = $this->botProvider->getBot()->setWebhook($url);

        if ($isSet) {
            Command::output('Webhook updated');
            return;
        }

        Command::output('Webhook updating failure');
    }
}
