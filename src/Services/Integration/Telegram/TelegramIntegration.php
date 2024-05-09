<?php

namespace Jegulnomic\Services\Integration\Telegram;

use DI\Attribute\Inject;
use Jegulnomic\Systems\Rest;

readonly class TelegramIntegration
{
    private const string TELEGRAM_API_URL = 'https://api.telegram.org/bot%s/%s';

    private string $token;

    public function __construct(
        #[Inject(Rest::class)]
        private Rest $rest
    ) {
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function __call(string $name, array $arguments): array
    {
        if (empty($arguments)) {
            throw new \RuntimeException('Not enough arguments.');
        }

        if (!is_array($arguments[0])) {
            throw new \RuntimeException('First argument must be a payload array.');
        }

        $result = $this->rest->post(
            sprintf(self::TELEGRAM_API_URL, $this->token, $name),
            $arguments[0]
        );

        return json_decode($result, true);
    }
}
