<?php

namespace Jegulnomic\Services;

use DI\Attribute\Inject;
use Jegulnomic\Entity\Income;
use Jegulnomic\Entity\Withdrawal;
use Jegulnomic\Enum\Currencies;
use Jegulnomic\Enum\Withdrawal\Status;
use Jegulnomic\Services\Integration\GeorgianCentralBankIntegration;
use Jegulnomic\Systems\Calculator\DecimalCalculator;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;
use Jegulnomic\ValueObject\Money;
use Ramsey\Uuid\Uuid;

readonly class WithdrawalCreator
{
    public const CALCULATION_ACCURACY = 4;

    public function __construct(
        #[Inject(DatabaseStorage::class)]
        private StorageInterface $storage,
        #[Inject(LariConverter::class)]
        private LariConverter $lariConverter,
        #[Inject(GeorgianCentralBankIntegration::class)]
        private GeorgianCentralBankIntegration $georgianCentralBankIntegration,
    ) {
    }

    public function create(Income $income): Withdrawal
    {
        $rateToLari = $this->georgianCentralBankIntegration->getCurrency($income->currency, $income->date)->getRate();
        $lari = $this->lariConverter->convertTo(
            Currencies::GEL,
            $income->getMoney(),
            $rateToLari
        );

        $tax = new Money(
            bcmul($lari->amount, '0.01', self::CALCULATION_ACCURACY),
            Currencies::GEL,
            $income->date
        );

        $interest = new Money(
            bcmul($lari->amount, '0.07', self::CALCULATION_ACCURACY),
            Currencies::GEL,
            $income->date
        );

        $result = new Money(
            (new DecimalCalculator($lari->amount))
                ->subtract($tax->amount)
                ->subtract($interest->amount)
                ->getResult(),
            Currencies::GEL,
            $income->date
        );

        $lariToRub = $this->georgianCentralBankIntegration->getCurrency(
            Currencies::RUB,
            $income->date
        )->getRate();

        $rubbles = new Money(
            (new DecimalCalculator($result->amount))
                ->divide($lariToRub)
                ->multiply('100')
                ->getResult(),
            Currencies::RUB,
            $income->date
        );

        $withdrawal = new Withdrawal(
            Uuid::uuid4(),
            $rateToLari,
            $lari->amount,
            $tax->amount,
            $interest->amount,
            $result->amount,
            $lariToRub,
            $rubbles->amount,
            Status::PENDING,
            null,
            null
        );

        $this->storage->save($withdrawal);

        return $withdrawal;
    }
}
