<?php

namespace Jegulnomic\DTO\GeorgianCentralBankIntegration;

use DateTimeInterface;
use Jegulnomic\DTO\Interfaces\RateContainerInterface;

class Currency implements RateContainerInterface
{
    private string $code;

    private int $quantity;

    private string $rateFormated;

    private string $diffFormated;

    private string $rate;

    private string $name;

    private string $diff;

    private \DateTimeInterface $date;

    private \DateTimeInterface $validFromDate;

    /**
     * @param string $code
     * @param int $quantity
     * @param string $rateFormated
     * @param string $diffFormated
     * @param string $rate
     * @param string $name
     * @param string $diff
     * @param \DateTimeInterface $date
     * @param \DateTimeInterface $validFromDate
     */
    public function __construct(
        string $code,
        int $quantity,
        string $rateFormated,
        string $diffFormated,
        string $rate,
        string $name,
        string $diff,
        \DateTimeInterface $date,
        \DateTimeInterface $validFromDate
    ) {
        $this->code = $code;
        $this->quantity = $quantity;
        $this->rateFormated = $rateFormated;
        $this->diffFormated = $diffFormated;
        $this->rate = $rate;
        $this->name = $name;
        $this->diff = $diff;
        $this->date = $date;
        $this->validFromDate = $validFromDate;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getRateFormated(): string
    {
        return $this->rateFormated;
    }

    public function getDiffFormated(): string
    {
        return $this->diffFormated;
    }

    public function getRate(): string
    {
        return $this->rate;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDiff(): string
    {
        return $this->diff;
    }

    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    public function getValidFromDate(): DateTimeInterface
    {
        return $this->validFromDate;
    }
}