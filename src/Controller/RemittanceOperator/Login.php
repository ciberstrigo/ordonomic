<?php

namespace Jegulnomic\Controller\RemittanceOperator;

use DI\Attribute\Inject;
use Jegulnomic\Systems\Authenticator;
use Jegulnomic\Systems\Template\Flash;
use Jegulnomic\Systems\Template\Template;
use SergiX44\Nutgram\Nutgram;

readonly class Login
{
    public function __construct(
        #[Inject(Nutgram::class)]
        private Nutgram $telegram
    ) {
    }

    public function index()
    {
        $isLoginSuccess = false;

        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $isLoginSuccess = $this->loginUser();
        }

        return (new Template())->render(
            'src/Templates/pages/login.phtml',
            [
                'telegram_user_id' => $_GET['telegram_user_id'] ?? $_POST['telegram-user-id'],
                'close' => $isLoginSuccess,
            ]
        );
    }

    private function loginUser(): bool
    {
        try {
            $operator = Authenticator::authenticate($_POST['telegram-user-id'], $_POST['password']);
        } catch (\Throwable $e) {
            Flash::createFlash(
                'login',
                $e->getMessage(),
                Flash::FLASH_ERROR
            );

            return false;
        }

        Flash::createFlash(
            'login',
            'Successful registered. Proceed back to telegram bot. You can close this page now.',
            Flash::FLASH_SUCCESS
        );

        $this->telegram
            ->sendMessage(
                'Вы вошли в систему.',
                $operator->telegramUserId
            );

        return true;
    }
}
