<?php

namespace Jegulnomic\Controller;

abstract class BaseTelegramFileProviderController
{
    public function index(): string
    {
        $url = sprintf(
            'https://api.telegram.org/file/bot%s/%s',
            $this->getTelegramBotToken(),
            $_REQUEST['path']
        );

        if ($this->getContentType()) {
            header("Content-Type: " . $this->getContentType());
        }

        return file_get_contents($url);
    }

    abstract protected function getTelegramBotToken(): string;

    protected function getContentType(): ?string
    {
        return null;
    }
}