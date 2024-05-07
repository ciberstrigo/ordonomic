<?php

namespace Jegulnomic\Services\Integration;

use Jegulnomic\DTO\GeorgianCentralBankIntegration\Currency;
use Jegulnomic\Systems\DTOCreator;
use Jegulnomic\Systems\Rest;

class GeorgianCentralBankIntegration
{
    public static function getCurrency(string $currency, \DateTimeInterface $date): Currency
    {
        $response = Rest::get('https://nbg.gov.ge/gw/api/ct/monetarypolicy/currencies/en/json/', [
            'currencies' => $currency,
            'date' => $date->format('Y-m-d')
        ]);

        $currencies = DTOCreator::createCurrencies(json_decode($response, true));

        return $currencies->getCurrencies()[0];
    }
}