<?php

namespace Jegulnomic\DTO\GeorgianCentralBankIntegration;

class Currencies
{
    private \DateTimeInterface $date;
    private array $currencies;

    public function __construct(\DateTimeInterface $date, array $currencies)
    {
        $this->date = $date;
        $this->currencies = $currencies;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function getCurrencies(): array
    {
        return $this->currencies;
    }
}
