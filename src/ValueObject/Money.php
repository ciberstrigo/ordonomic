<?php

namespace Jegulnomic\ValueObject;

readonly class Money
{
    public function __construct(
        public string $amount,
        public string $currency,
        public \DateTimeInterface $date
    ) {
    }
}
