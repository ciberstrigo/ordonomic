<?php

namespace Jegulnomic\Services\Authenticator;

use Jegulnomic\Controller\Customer\Login;
use Jegulnomic\Controller\Customer\Registration;
use Jegulnomic\Entity\Customer;
use Jegulnomic\Systems\BaseAuthenticator;
use Override;

class CustomerAuthenticator extends BaseAuthenticator
{
    #[Override]
    protected function getUserClass(): string
    {
        return Customer::class;
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
