<?php

namespace Jegulnomic\Controller\RemittanceOperator;

use DI\Attribute\Inject;
use Jegulnomic\Services\Authenticator\RemittanceOperatorAuthenticator;
use Jegulnomic\Services\Integration\Telegram\RemittanceOperator\BotProvider;
use Jegulnomic\Systems\BaseAuthenticator;
use Jegulnomic\Systems\Template\Flash;
use Jegulnomic\Systems\Template\Template;

readonly class Registration
{
    public function __construct(
        #[Inject(BotProvider::class)]
        private BotProvider $botProvider,
        #[Inject(RemittanceOperatorAuthenticator::class)]
        private BaseAuthenticator $authenticator
    ) {
    }
    public function index()
    {
        $isRegisterUserSuccess = false;

        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $isRegisterUserSuccess = $this->registerUser();
        }

        return (new Template())->render(
            'src/Templates/pages/RemittanceOperator/registration.phtml',
            [
                'telegram_user_id' => $_REQUEST['telegram_user_id'],
                'telegram_username' => $_REQUEST['telegram_username'],
                'close' => $isRegisterUserSuccess,
            ]
        );
    }

    private function registerUser(): bool
    {
        try {
            $operator = $this->authenticator
                ->register(
                    $_POST['telegram_user_id'],
                    $_POST['password']
                );
        } catch (\Throwable $e) {
            Flash::createFlash(
                'registration',
                'Can not register new operator. Server error.',
                Flash::FLASH_ERROR
            );

            return false;
        }

        Flash::createFlash(
            'registration',
            'Successful registered. Proceed back to telegram bot. You can close this page now.',
            Flash::FLASH_SUCCESS
        );

        $this->botProvider->getBot()
            ->sendMessage(
                'Регистрация завершена. Ожидайте подтверждения администратором.',
                $operator->telegramUserId
            );

        return true;
    }
}
