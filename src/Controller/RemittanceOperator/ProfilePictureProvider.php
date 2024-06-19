<?php

namespace Jegulnomic\Controller\RemittanceOperator;

use Jegulnomic\Controller\BaseTelegramFileProviderController;

class ProfilePictureProvider extends BaseTelegramFileProviderController
{
    protected function getTelegramBotToken(): string
    {
        return $_ENV['TELEGRAM_REMITTANCE_OPERATOR_BOT_TOKEN'];
    }

    protected function getContentType(): string
    {
        return 'image/jpeg';
    }
}