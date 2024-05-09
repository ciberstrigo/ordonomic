<?php

namespace Jegulnomic\Controller\RemittanceOperator;

use DI\Attribute\Inject;
use Jegulnomic\Services\Integration\Telegram\TelegramIntegration;
use Jegulnomic\Systems\Authenticator;
use Jegulnomic\Systems\Template\Flash;
use Jegulnomic\Systems\Template\Template;

readonly class Login
{
    public function __construct(
        #[Inject(TelegramIntegration::class)]
        private TelegramIntegration $telegramIntegration
    ) {
    }

    public function index()
    {
        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            try {
                $operator = Authenticator::authenticate($_POST['telegram-user-id'], $_POST['password']);
            } catch (\Throwable $e) {
                Flash::createFlash(
                    'login',
                    $e->getMessage(),
                    Flash::FLASH_ERROR
                );

                return (new Template())->render(
                    'src/Templates/pages/login.phtml',
                    ['telegram_user_id' => $_GET['telegram_user_id'] ?? $_POST['telegram-user-id']]
                );
            }

            Flash::createFlash(
                'login',
                'Successful registered. Proceed back to telegram bot. You can close this page now.',
                Flash::FLASH_SUCCESS
            );

            $this->telegramIntegration
                ->setToken($_ENV['TELEGRAM_REMITTANCE_OPERATOR_BOT_TOKEN'])
                ->sendMessage([
                    'chat_id' => $operator->telegramUserId,
                    'text' => 'Вы вошли в систему.'
                ]);
        }

        return (new Template())->render(
            'src/Templates/pages/login.phtml',
            ['telegram_user_id' => $_GET['telegram_user_id'] ?? $_POST['telegram-user-id']]
        );
    }
}
