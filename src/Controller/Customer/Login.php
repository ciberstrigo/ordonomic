<?php

namespace Jegulnomic\Controller\Customer;

use DI\Attribute\Inject;
use Jegulnomic\Services\Authenticator\RemittanceOperatorAuthenticator;
use Jegulnomic\Services\Integration\Telegram\AbstractBotProvider;
use Jegulnomic\Services\Integration\Telegram\RemittanceOperator\BotProvider;
use Jegulnomic\Systems\BaseAuthenticator;
use Jegulnomic\Systems\Template\Flash;
use Jegulnomic\Systems\Template\Template;
use SergiX44\Nutgram\Nutgram;

readonly class Login
{
    public function __construct(
        #[Inject(BotProvider::class)]
        private AbstractBotProvider $botProvider,
        #[Inject(RemittanceOperatorAuthenticator::class)]
        private BaseAuthenticator $authenticator
    ) {
    }

    public function index()
    {
        $isLoginSuccess = false;

        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $isLoginSuccess = $this->loginUser();
        }

        return (new Template())->render(
            'src/Templates/pages/Customer/login.phtml',
            [
                'telegram_user_id' => $_REQUEST['telegram_user_id'],
                'close' => $isLoginSuccess,
            ]
        );
    }

    private function loginUser(): bool
    {
        try {
            $operator = $this->authenticator->authenticate($_POST['telegram_user_id'], $_POST['password']);
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

        $this->botProvider
            ->getBot()
            ->sendMessage(
                'Вы вошли в систему.',
                $operator->telegramUserId
            );

        return true;
    }
}
