<?php

namespace Jegulnomic\Command\Telegram;

use DI\Attribute\Inject;
use Jegulnomic\Command\AbstractCommand;
use Jegulnomic\Controller\RemittanceOperator\Callback\TelegramBotCallback as OperatorCallback;
use Jegulnomic\Controller\Logger\Callback\TelegramBotCallback as LoggerCallback;
use Jegulnomic\Services\Integration\Telegram\AbstractBotProvider;
use Jegulnomic\Services\Integration\Telegram\RemittanceOperator\BotProvider as OperatorBotProvider;
use Jegulnomic\Services\Integration\Telegram\Logger\BotProvider as LoggerBotProvider;
use Jegulnomic\Services\Integration\Telegram\Webhook\WebhookUpdater;
use Jegulnomic\Systems\Command;
use Jegulnomic\Systems\PublicUrlProvider;

class UpdateWebhook extends AbstractCommand
{
    public function __construct(
        #[Inject(WebhookUpdater::class)]
        private WebhookUpdater $webhookUpdater,
    ) {
    }

    public function forRemittanceOperator(): void
    {
        Command::output('Updating webhook to Remittance Operator');

        try {
            $this->webhookUpdater->forRemittanceOperator();
        } catch (\Throwable) {
            Command::output('Webhook was not set. Error occurred.');
        }
        Command::output('Webhook updated');
    }

    public function forLogger(): void
    {
        Command::output('Updating webhook to Logger');

        try {
            $this->webhookUpdater->forLogger();
        } catch (\Throwable) {
            Command::output('Webhook was not set. Error occurred.');
        }

        Command::output('Webhook updated');
    }

    public function forCustomer(): void
    {
        Command::output('Updating webhook to Customer');

        try {
            $this->webhookUpdater->forCustomer();
        } catch (\Throwable) {
            Command::output('Webhook was not set. Error occurred.');
        }

        Command::output('Webhook updated');
    }
}
