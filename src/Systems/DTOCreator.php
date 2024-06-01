<?php

namespace Jegulnomic\Systems;

use Jegulnomic\DTO\GeorgianCentralBankIntegration\Currencies;
use Jegulnomic\DTO\GeorgianCentralBankIntegration\Currency;

class DTOCreator
{
    public const DATE_FORMAT = 'Y-m-d\TH:i:s.u\Z';

    public static function createCurrencies(array $associative): Currencies
    {
        if (empty($associative)) {
            throw new \LogicException();
        }

        $associative = $associative[0];

        $currencies = [];
        if (!isset($associative['currencies'])) {
            throw new \LogicException('No "currency" field specified');
        }

        foreach ($associative['currencies'] as $currency) {
            $currencies[] = new Currency(
                $currency['code'],
                $currency['quantity'],
                $currency['rateFormated'],
                $currency['diffFormated'],
                $currency['rate'],
                $currency['name'],
                $currency['diff'],
                self::getDateTimeFromString($currency['date']),
                self::getDateTimeFromString($currency['validFromDate']),
            );
        }

        return new Currencies(
            self::getDateTimeFromString($associative['date']),
            $currencies
        );
    }

    private static function getDateTimeFromString(string $dateTime): \DateTimeInterface
    {
        $date = \DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $dateTime);

        if (false === $date) {
            throw new \LogicException('Неверный формат даты и времени');
        }

        return $date;
    }
}
