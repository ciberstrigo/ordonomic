<?php

namespace Jegulnomic\Services\Integration\Telegram;

use Jegulnomic\Systems\Rest;

class TelegramIntegration
{
    private const string TELEGRAM_API_URL = 'https://api.telegram.org/bot%s/%s';

    public function __construct(protected readonly string $token)
    {
    }

    public function __call(string $name, array $arguments): array
    {
        if (empty($arguments)) {
            throw new \RuntimeException('Not enough arguments.');
        }

        if (!is_array($arguments[0])) {
            throw new \RuntimeException('First argument must be a payload array.');
        }

        $result = Rest::post(
            sprintf(self::TELEGRAM_API_URL, $this->token, $name),
            $arguments[0]
        );

        return json_decode($result, true);
    }
}
