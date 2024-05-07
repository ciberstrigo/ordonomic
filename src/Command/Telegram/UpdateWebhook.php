<?php

namespace Jegulnomic\Command\Telegram;

use Jegulnomic\Command\AbstractCommand;
use Jegulnomic\Services\Integration\Telegram\TelegramIntegration;
use Jegulnomic\Systems\Command;

class UpdateWebhook extends AbstractCommand
{
    public function forRemittanceOperator()
    {
        $response = (new TelegramIntegration($_ENV['TELEGRAM_REMITTANCE_OPERATOR_BOT_TOKEN']))
            ->setWebhook([
                'url' => $this->arguments[0]
            ]);

        Command::output($response['description']);
    }
}
