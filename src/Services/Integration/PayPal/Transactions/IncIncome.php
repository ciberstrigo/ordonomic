<?php

namespace Jegulnomic\Services\Integration\PayPal\Transactions;

use Jegulnomic\ValueObject\Money;
use Override;

class IncIncome implements TransactionInterface
{
    public function __construct(
        public string $from,
        public string $id,
        public \DateTimeInterface $date,
        public Money $amount,
    ) {
    }

    #[Override]
    public function getRecipient(): string
    {
        return $this->from;
    }

    #[Override]
    public function getAmount(): Money
    {
        return $this->amount;
    }

    #[Override]
    public function getTransactionDate(): \DateTimeInterface
    {
        return $this->date;
    }

    #[Override]
    public function getTransactionId(): string
    {
        return $this->id;
    }

    #[Override]
    public static function getType(): string
    {
        return 'INC';
    }
}
