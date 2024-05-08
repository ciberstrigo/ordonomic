<?php

namespace Jegulnomic\Systems;

use Jegulnomic\Systems\Controller\ControllerManager;

class PublicUrlProvider
{
    public static function getUrl()
    {
        if ('DEV' === strtoupper($_ENV['APP_ENV'])) {
            $result = Rest::get('http://ngrok:4040/api/tunnels');
            $data = json_decode($result, true);

            return $data['tunnels'][0]['public_url'];
        }

        return $_ENV['PROD_BASE_URL'];
    }

    public static function getTelegramWebhookUrl(string $webhookController): string
    {
        return self::getUrl() . ControllerManager::getUrlPath($webhookController);
    }
}
