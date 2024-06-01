<?php

namespace Jegulnomic\Entity;

use Jegulnomic\Services\Integration\PayPal\Transactions\IncIncome;
use Jegulnomic\Services\Integration\PayPal\Transactions\P2PIncome;
use Jegulnomic\Services\Integration\PayPal\Transactions\TransactionInterface;
use Jegulnomic\Systems\Database\Attributes\Column;
use Jegulnomic\Systems\Database\Attributes\Table;
use Jegulnomic\ValueObject\Money;
use Ramsey\Uuid\Uuid;

#[Table(name: 'income')]
class Income
{
    public function __construct(
        #[Column(name: 'id')]
        readonly public string $id,
        #[Column(name: 'type')]
        readonly public string $type,
        #[Column(name: 'transaction_id')]
        readonly public string $transactionId,
        #[Column(name: 'date')]
        readonly public \DateTimeInterface $date,
        #[Column(name: 'amount')]
        readonly public string $amount,
        #[Column(name: 'currency')]
        readonly public string $currency,
        #[Column(name: 'from')]
        readonly public string $from,
        #[Column(name: 'withdrawal_id')]
        public ?Withdrawal $withdrawal
    ) {
    }

    public function getMoney(): Money
    {
        return new Money(
            $this->amount,
            $this->currency,
            $this->date
        );
    }

    public static function createFromPayPalTransaction(
        TransactionInterface $transaction
    ): self {
        return new self(
            Uuid::uuid4(),
            $transaction::getType(),
            $transaction->getTransactionId(),
            $transaction->getTransactionDate(),
            $transaction->getAmount()->amount,
            $transaction->getAmount()->currency,
            $transaction->getRecipient(),
            null
        );
    }
}
