<?php

namespace Jegulnomic\Entity;

use Jegulnomic\Systems\Database\Attributes\Column;
use Jegulnomic\Systems\Database\Attributes\Table;
use Override;
use Ramsey\Uuid\Uuid;

#[Table(name: 'customer')]
class Customer implements UserInterface
{
    private const DEFAULT_HOLDS_PER_DAY = 3;

    public function __construct(
        #[Column(name: 'id')]
        public string $id,
        #[Column(name: 'telegram_user_id')]
        public string $telegramUserId,
        #[Column(name: 'session_until')]
        public int $sessionUntil,
        #[Column(name: 'password')]
        public string $passwordHashed,
        #[Column(name: 'holds_left')]
        public int $holdsLeft,
        #[Column(name: 'last_hold_at')]
        public ?\DateTimeInterface $lastHoldAt,
        #[Column(name: 'registered_at')]
        public \DateTimeInterface $registeredAt
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
            self::DEFAULT_HOLDS_PER_DAY,
            null,
            new \DateTimeImmutable('now')
        );
    }

    public function isAllowToProceed(): bool
    {
        return $this->sessionUntil > time();
    }

    #[Override]
    public function endSession(): void
    {
        $this->sessionUntil = 0;
    }
}
