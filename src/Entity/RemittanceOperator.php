<?php

namespace Jegulnomic\Entity;

use Jegulnomic\Systems\Database\Attributes\Column;
use Jegulnomic\Systems\Database\Attributes\Table;

#[Table(name: 'remittance_operator')]
class RemittanceOperator
{
    public function __construct(
        #[Column(name: 'id')]
        public string $id,
        #[Column(name: 'telegram_user_id')]
        public string $telegramUserId,
        #[Column(name: 'session_until')]
        public int $sessionUntil,
        #[Column(name: 'password')]
        public string $passwordHashed,
        #[Column(name: 'is_verified')]
        public string $isVerified,
    ) {
    }

    public function isAllowToProceed()
    {
        return $this->sessionUntil > time() && $this->isVerified;
    }
}
