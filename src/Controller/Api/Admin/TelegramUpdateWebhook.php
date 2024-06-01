<?php

namespace Jegulnomic\Controller\Api\Admin;

use DI\Attribute\Inject;
use Jegulnomic\Controller\Logger\Callback\TelegramBotCallback as LoggerCallback;
use Jegulnomic\Controller\RemittanceOperator\Callback\TelegramBotCallback as OperatorCallback;
use Jegulnomic\Services\Integration\Telegram\AbstractBotProvider;
use Jegulnomic\Services\Integration\Telegram\Logger\BotProvider as LoggerBotProvider;
use Jegulnomic\Services\Integration\Telegram\RemittanceOperator\BotProvider as OperatorBotProvider;
use Jegulnomic\Services\Integration\Telegram\Webhook\WebhookUpdater;
use Jegulnomic\Systems\PublicUrlProvider;

readonly class TelegramUpdateWebhook
{
    public function __construct(
        #[Inject(WebhookUpdater::class)]
        private WebhookUpdater $webhookUpdater,
    ) {
        header('Content-Type: application/json; charset=utf-8');
    }

    public function forRemittanceOperator(): void
    {
        try {
            $this->webhookUpdater->forRemittanceOperator();
        } catch (\Throwable) {
            echo json_encode([
                'status' => 'fail',
                'message' => 'webhook updating failed'
            ]);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'webhook updated'
        ]);
    }

    public function forLogger(): void
    {
        try {
            $this->webhookUpdater->forLogger();
        } catch (\Throwable) {
            echo json_encode([
                'status' => 'fail',
                'message' => 'webhook updating failed'
            ]);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'webhook updated'
        ]);
    }
}