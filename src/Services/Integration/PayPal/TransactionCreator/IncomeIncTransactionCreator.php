<?php

namespace Jegulnomic\Services\Integration\PayPal\TransactionCreator;

use Jegulnomic\Services\Integration\PayPal\Transactions\IncIncome;
use Jegulnomic\Services\Integration\PayPal\Transactions\TransactionInterface;
use Jegulnomic\ValueObject\Money;
use Override;

class IncomeIncTransactionCreator extends AbstractTransactionCreator implements TransactionCreatorInterface
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

        return new IncIncome(
            $this->extractNameFromHeader($header),
            $id,
            $dateObject,
            $this->stringToMoney($amount, $dateObject),
        );
    }

    protected function getHeaderExtractionPattern(): string
    {
        return '/(.+).has sent you.(.+)\./u';
    }

    protected function getNameMatchedPosition(): int
    {
        return 1;
    }
}
