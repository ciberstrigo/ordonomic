<?php

namespace Jegulnomic\Command\Telegram;

use DI\Attribute\Inject;
use Jegulnomic\Command\AbstractCommand;
use Jegulnomic\Controller\Callback\TelegramBotRemittanceOperatorCallback;
use Jegulnomic\Services\Integration\Telegram\TelegramIntegration;
use Jegulnomic\Systems\Command;
use Jegulnomic\Systems\PublicUrlProvider;

class UpdateWebhook extends AbstractCommand
{
    public function __construct(
        #[Inject(PublicUrlProvider::class)]
        private readonly PublicUrlProvider $publicUrlProvider,
        #[Inject(TelegramIntegration::class)]
        private readonly TelegramIntegration $telegramIntegration
    )
    {}

    public function forRemittanceOperator()
    {
        $url = $this->publicUrlProvider->getTelegramWebhookUrl(
            TelegramBotRemittanceOperatorCallback::class
        );

        Command::output('Updating webhook to: ' . $url);

        $response =
            $this->telegramIntegration
                ->setToken($_ENV['TELEGRAM_REMITTANCE_OPERATOR_BOT_TOKEN'])
                ->setWebhook([
                    'url' => $url
                ]);

        Command::output($response['description']);
    }
}
