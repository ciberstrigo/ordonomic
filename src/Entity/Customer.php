<?php

namespace Jegulnomic\Entity;

use Jegulnomic\Systems\Database\Attributes\Column;
use Override;
use Ramsey\Uuid\Uuid;

class Customer implements UserInterface
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
    ) {
    }

    #[Override]
    public static function register(
        int $telegramId,
        int $sessionUntil,
        string $passwordHashed
    ): UserInterface {
        return new self(
            Uuid::uuid4(),
            $telegramId,
            $sessionUntil,
            $passwordHashed,
        );
    }

    #[Override]
    public function endSession(): void
    {
        $this->sessionUntil = 0;
    }
}
