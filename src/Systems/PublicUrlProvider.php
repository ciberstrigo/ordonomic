<?php

namespace Jegulnomic\Systems;

use DI\Attribute\Inject;
use Jegulnomic\Systems\Controller\ControllerManager;

readonly class PublicUrlProvider
{
    public function __construct(
        #[Inject(ControllerManager::class)]
        private ControllerManager $controllerManager
    ) {
    }

    public function getUrl(): string
    {
        if ('local' === strtolower($_ENV['APP_ENV'])) {
            $result = Rest::get('http://ngrok:4040/api/tunnels');
            $data = json_decode($result, true);

            return $data['tunnels'][0]['public_url'];
        }

        if ('prod' === strtolower($_ENV['APP_ENV'])) {
            return $_ENV['PROD_BASE_URL'];
        }

        return '';
    }

    public function getControllerUrl(string $controllerClass): string
    {
        return $this->getUrl() . $this->controllerManager->getUrlPath($controllerClass);
    }
}
