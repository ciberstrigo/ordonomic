<?php

namespace Jegulnomic\Services;

use Jegulnomic\Entity\Income;
use Jegulnomic\Entity\Withdrawal;
use Jegulnomic\Enum\Currencies;
use Jegulnomic\Enum\Withdrawal\Status;
use Jegulnomic\Services\Integration\GeorgianCentralBankIntegration;
use Jegulnomic\Systems\Calculator\DecimalCalculator;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\ValueObject\Money;
use Ramsey\Uuid\Uuid;

class WithdrawalCreator
{
    public const int CALCULATION_ACCURACY = 4;

    public function create(Income $income): Withdrawal
    {
        $rateToLari = GeorgianCentralBankIntegration::getCurrency($income->currency, $income->date)->getRate();
        $lari = LariConverter::convertTo(
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

        $lariToRub = GeorgianCentralBankIntegration::getCurrency(
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

        DatabaseStorage::i()->save($withdrawal);

        return $withdrawal;
    }
}
