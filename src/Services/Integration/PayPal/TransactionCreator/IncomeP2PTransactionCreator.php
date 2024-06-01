<?php

namespace Jegulnomic\Services\Integration\PayPal\TransactionCreator;

use Jegulnomic\Services\Integration\PayPal\Transactions\P2PIncome;
use Jegulnomic\Services\Integration\PayPal\Transactions\TransactionInterface;
use Override;

class IncomeP2PTransactionCreator extends AbstractTransactionCreator implements TransactionCreatorInterface
{
    #[Override]
    public function create(
        string $header,
        string $id,
        string $date,
        string $amount,
        string $fee,
        string $total
    ): TransactionInterface {
        $dateObject = \DateTimeImmutable::createFromFormat('d F Y', $date);

        return new P2PIncome(
            self::extractNameFromHeader($header),
            $id,
            $dateObject,
            $this->stringToMoney($amount, $dateObject),
            $this->stringToMoney($fee, $dateObject),
            $this->stringToMoney($total, $dateObject),
        );
    }

    protected function getHeaderExtractionPattern(): string
    {
        return '/Accept your (.+) from (.+)/u';
    }

    protected function getNameMatchedPosition(): int
    {
        return 2;
    }
}
