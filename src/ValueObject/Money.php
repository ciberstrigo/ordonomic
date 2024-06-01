<?php

namespace Jegulnomic\ValueObject;

use Jegulnomic\Enum\Currencies;

readonly class Money
{
    public function __construct(
        public string $amount,
        public string $currency,
        public \DateTimeInterface $date
    ) {
    }

    public function isEmpty(): bool
    {
        return $this->currency === Currencies::EMPTY;
    }

    public static function empty(): self
    {
        return new Money(
            '0.00',
            Currencies::EMPTY,
            new \DateTimeImmutable('now')
        );
    }
}
