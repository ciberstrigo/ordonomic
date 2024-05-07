<?php

namespace Jegulnomic\Systems\Calculator;

use Override;

class DecimalCalculator implements CalculatorInterface
{
    public const int ACCURACY = 4;

    public function __construct(private string $decimal)
    {
    }

    #[Override]
    public function add(string $decimal): CalculatorInterface
    {
        $this->decimal = bcadd($this->decimal, $decimal, self::ACCURACY);

        return $this;
    }

    #[Override]
    public function subtract(string $decimal): CalculatorInterface
    {
        $this->decimal = bcsub($this->decimal, $decimal, self::ACCURACY);

        return $this;
    }

    #[Override]
    public function divide(string $decimal): CalculatorInterface
    {
        $this->decimal = bcdiv($this->decimal, $decimal, self::ACCURACY);

        return $this;
    }

    #[Override]
    public function multiply(string $decimal): CalculatorInterface
    {
        $this->decimal = bcmul($this->decimal, $decimal, self::ACCURACY);

        return $this;
    }

    #[Override]
    public function getResult(): string
    {
        return $this->decimal;
    }
}
