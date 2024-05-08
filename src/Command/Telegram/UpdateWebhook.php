<?php

namespace Jegulnomic\Command\Telegram;

use Jegulnomic\Command\AbstractCommand;
use Jegulnomic\Controller\Callback\TelegramBotRemittanceOperatorCallback;
use Jegulnomic\Services\Integration\Telegram\TelegramIntegration;
use Jegulnomic\Systems\Command;
use Jegulnomic\Systems\PublicUrlProvider;

class UpdateWebhook extends AbstractCommand
{
    public function forRemittanceOperator()
    {
        $response = (new TelegramIntegration($_ENV['TELEGRAM_REMITTANCE_OPERATOR_BOT_TOKEN']))
            ->setWebhook([
                'url' => PublicUrlProvider::getTelegramWebhookUrl(
                    TelegramBotRemittanceOperatorCallback::class
                )
            ]);

        Command::output($response['description']);
    }
}
