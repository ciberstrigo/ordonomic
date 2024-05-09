<?php

namespace Jegulnomic\Controller\Callback;

use DI\Attribute\Inject;
use Jegulnomic\Services\Integration\Telegram\RemittanceOperatorMessageHandler;
use Jegulnomic\Services\Integration\Telegram\TelegramIntegration;

class TelegramBotRemittanceOperatorCallback
{
    public function __construct(
        #[Inject(RemittanceOperatorMessageHandler::class)]
        private readonly RemittanceOperatorMessageHandler $messageHandler
    )
    {
    }

    public function index()
    {
        try {
            $telegram = new TelegramIntegration($_ENV['TELEGRAM_REMITTANCE_OPERATOR_BOT_TOKEN']);
            $content = file_get_contents("php://input");
            $update = json_decode($content, true);

            if (null === $update) {
                http_send_status(404);
            }

            if ($this->isNewMessage($update)) {
                if ($command = $this->getCommandFromUpdate($update)) {
                    if (method_exists($this->messageHandler, $command)) {
                        $this->messageHandler->$command();
                        return;
                    }
                }
            }

            if ($this->isCallbackQuery($update)) {
                if (method_exists($this->messageHandler, 'callbackQuery')) {
                    $this->messageHandler->callbackQuery();
                    return;
                }
            }

            $telegram->sendMessage([
                'chat_id' => $update['message']['from']['id'],
                'text' => 'Command not found',
            ]);

        } catch (\Throwable $e) {
            $telegram->sendMessage([
                'chat_id' => 6031405926,
                'text' => $e->getMessage()
            ]);
        }
    }

    private function getCommandFromUpdate(array $update): ?string
    {
        $text = $update['message']['text'] ?? '';

        if (!preg_match('/\/([A-Za-z0-9]+)/u', $text, $matches)) {
            return null;
        }

        return $matches[1];
    }

    private function isCallbackQuery(array $update): bool
    {
        return array_key_exists('update_id', $update) && array_key_exists('callback_query', $update);
    }

    private function isNewMessage(array $update): bool
    {
        return array_key_exists('message', $update);
    }
}
