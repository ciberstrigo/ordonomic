<?php

namespace Jegulnomic\Services;

use Jegulnomic\Systems\Calculator\DecimalCalculator;
use Jegulnomic\ValueObject\Money;

class LariConverter
{
    public static function convertTo(string $currency, Money $money, string $rate): Money
    {
        return new Money(
            (new DecimalCalculator($money->amount))->multiply($rate)->getResult(),
            $currency,
            $money->date
        );
    }

    public static function convertFrom(string $currency, Money $money, string $rate): Money
    {
        return new Money(
            (new DecimalCalculator($money->amount))->divide($rate)->getResult(),
            $currency,
            $money->date
        );
    }
}