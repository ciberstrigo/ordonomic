<?php

namespace Jegulnomic\Controller\Api\Admin;

use DI\Attribute\Inject;
use Jegulnomic\Services\Integration\Telegram\Webhook\WebhookUpdater;

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
        } catch (\Throwable $e) {
            echo json_encode([
                'status' => 'fail',
                'message' => $e->getMessage(),
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
        } catch (\Throwable $e) {
            echo json_encode([
                'status' => 'fail',
                'message' => $e->getMessage()
            ]);
            return;
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'webhook updated'
        ]);
    }
}
