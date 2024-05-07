<?php

namespace Jegulnomic\Services;

use Jegulnomic\Entity\Income;
use Jegulnomic\ValueObject\Money;
use Ramsey\Uuid\Uuid;

class IncomeFromParsedPayPalMailCreator
{
    public static function create(
        string $transactionId,
        string $money,
        string $date,
        string $from,
    ): Income {
        $moneyValueObject = self::createMoneyFromString($money, $date);

        return new Income(
            UUID::uuid4(),
            $transactionId,
            $moneyValueObject->date,
            $moneyValueObject->amount,
            $moneyValueObject->currency,
            $from,
            null
        );
    }

    private static function createMoneyFromString(string $money, string $date): Money
    {
        // ["USD"]
        if (!preg_match('/[A-Z]+$/u', $money, $currencyMatches)) {
            throw new \RuntimeException('Invalid $money string. Currency not found.');
        }

        // ["$30,54","30,54"]
        if (!preg_match('/^.([0-9]+,[0-9]{2})/u', $money, $amountMatches)) {
            throw new \RuntimeException('Invalid $money string. Amount not found.');
        }

        return new Money(
            str_replace(',', '.', $amountMatches[1]),
            $currencyMatches[0],
            \DateTimeImmutable::createFromFormat('d F Y', $date)
        );
    }
}