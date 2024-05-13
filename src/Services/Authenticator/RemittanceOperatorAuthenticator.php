<?php

namespace Jegulnomic\Services\Authenticator;

use Jegulnomic\Controller\RemittanceOperator\Login;
use Jegulnomic\Controller\RemittanceOperator\Registration;
use Jegulnomic\Entity\RemittanceOperator;
use Jegulnomic\Systems\BaseAuthenticator;
use Override;

class RemittanceOperatorAuthenticator extends BaseAuthenticator
{
    #[Override]
    protected function getUserClass(): string
    {
        return RemittanceOperator::class;
    }

    #[Override]
    protected function getLoginController(): string
    {
        return Login::class;
    }

    #[Override]
    protected function getRegistrationController(): string
    {
        return Registration::class;
    }
}
