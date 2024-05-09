<?php

namespace Jegulnomic\Systems;

use DI\Attribute\Inject;
use Jegulnomic\Systems\Controller\ControllerManager;

class PublicUrlProvider
{
    public function __construct(
        #[Inject(ControllerManager::class)]
        private readonly ControllerManager $controllerManager
    )
    {
    }

    public function getUrl()
    {
        if ('DEV' === strtoupper($_ENV['APP_ENV'])) {
            $result = Rest::get('http://ngrok:4040/api/tunnels');
            $data = json_decode($result, true);

            return $data['tunnels'][0]['public_url'];
        }

        return $_ENV['PROD_BASE_URL'];
    }

    public function getTelegramWebhookUrl(string $webhookController): string
    {
        return self::getUrl() . $this->controllerManager->getUrlPath($webhookController);
    }
}
