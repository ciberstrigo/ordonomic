<?php

namespace Jegulnomic\Services\Integration;

use DI\Attribute\Inject;
use Jegulnomic\DTO\GeorgianCentralBankIntegration\Currency;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\DTOCreator;
use Jegulnomic\Systems\Rest;
use Jegulnomic\Systems\StorageInterface;

class GeorgianCentralBankIntegration
{
    public function __construct(
        #[Inject(DatabaseStorage::class)]
        protected readonly StorageInterface $storage,
        #[Inject(Rest::class)]
        protected readonly Rest $rest,
        #[Inject(DTOCreator::class)]
        protected readonly DTOCreator $DTOCreator,
    )
    {}

    public function getCurrency(string $currency, \DateTimeInterface $date): Currency
    {
        $response = $this->rest->get('https://nbg.gov.ge/gw/api/ct/monetarypolicy/currencies/en/json/', [
            'currencies' => $currency,
            'date' => $date->format('Y-m-d')
        ]);

        $currencies = $this->DTOCreator->createCurrencies(json_decode($response, true));

        return $currencies->getCurrencies()[0];
    }
}
