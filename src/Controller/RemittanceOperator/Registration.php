<?php

namespace Jegulnomic\Controller\RemittanceOperator;

use DI\Attribute\Inject;
use Jegulnomic\Services\Integration\Telegram\TelegramIntegration;
use Jegulnomic\Systems\Authenticator;
use Jegulnomic\Systems\Template\Flash;
use Jegulnomic\Systems\Template\Template;

readonly class Registration
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
                $operator = Authenticator::register($_POST['telegram-user-id'], $_POST['password']);
                if (null === $operator) {
                    throw new \RuntimeException('Can not register new operator. Server error.');
                }
            } catch (\Throwable $e) {
                Flash::createFlash(
                    'registration',
                    $e->getMessage(),
                    Flash::FLASH_ERROR
                );
            }

            Flash::createFlash(
                'registration',
                'Successful registered. Proceed back to telegram bot. You can close this page now.',
                Flash::FLASH_SUCCESS
            );

            $this->telegramIntegration
                ->setToken($_ENV['TELEGRAM_REMITTANCE_OPERATOR_BOT_TOKEN'])
                ->sendMessage([
                    'chat_id' => $operator->telegramUserId,
                    'text' => 'Регистрация завершена. Ожидайте подтверждения администратором.'
                ]);
        }

        return (new Template())->render(
            'src/Templates/pages/registration.phtml',
            ['telegram_user_id' => $_GET['telegram_user_id'] ?? $_POST['telegram-user-id']]
        );
    }
}
