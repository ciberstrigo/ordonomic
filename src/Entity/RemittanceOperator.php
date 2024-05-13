<?php

namespace Jegulnomic\Entity;

use Jegulnomic\Systems\Database\Attributes\Column;
use Jegulnomic\Systems\Database\Attributes\Table;
use Override;
use Ramsey\Uuid\Uuid;

#[Table(name: 'remittance_operator')]
class RemittanceOperator implements UserInterface
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

    public function isAllowToProceed(): bool
    {
        return $this->sessionUntil > time() && $this->isVerified;
    }

    #[Override]
    public static function register(int $telegramId, int $sessionUntil, string $passwordHashed): UserInterface
    {
        return new RemittanceOperator(
            Uuid::uuid4(),
            $telegramId,
            $sessionUntil,
            $passwordHashed,
            0
        );
    }

    public function endSession(): void
    {
        $this->sessionUntil = 0;
    }
}
