<?php

namespace Jegulnomic\Services\Integration\PayPal\Transactions;

use Jegulnomic\ValueObject\Money;

interface TransactionInterface
{
    public static function getType(): string;

    public function getRecipient(): string;

    public function getAmount(): Money;

    public function getTransactionDate(): \DateTimeInterface;

    public function getTransactionId(): string;
}
