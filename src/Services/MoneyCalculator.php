<?php

namespace Jegulnomic\Services;

use Jegulnomic\Systems\Calculator\CalculatorInterface;
use Jegulnomic\Systems\Calculator\DecimalCalculator;
use Jegulnomic\ValueObject\Money;

class MoneyCalculator
{
    public function __construct(
        protected Money $result
    )
    {

    }

    public function add(Money $money): self
    {
        if ($money->currency !== $this->result->currency) {
            throw new \LogicException('Operation with different currencies is not allowed.');
        }

        $this->result = new Money(
            (new DecimalCalculator($this->result->amount))->add($money->amount)->getResult(),
            $this->result->currency,
            $this->result->date
        );

        return $this;
    }

    public function subtract(Money $money): self
    {
        if ($money->currency !== $this->result->currency) {
            throw new \LogicException('Operation with different currencies is not allowed.');
        }

        $this->result = new Money(
            (new DecimalCalculator($this->result->amount))->add($money->amount)->getResult(),
            $this->result->currency,
            $this->result->date
        );

        return $this;
    }

    public function divide(Money $money): self
    {
        if ($money->currency !== $this->result->currency) {
            throw new \LogicException('Operation with different currencies is not allowed.');
        }

        $this->result = new Money(
            (new DecimalCalculator($this->result->amount))->divide($money->amount)->getResult(),
            $this->result->currency,
            $this->result->date
        );

        return $this;
    }

    public function multiply(Money $money): self
    {
        if ($money->currency !== $this->result->currency) {
            throw new \LogicException('Operation with different currencies is not allowed.');
        }

        $this->result = new Money(
            (new DecimalCalculator($this->result->amount))->multiply($money->amount)->getResult(),
            $this->result->currency,
            $this->result->date
        );

        return $this;
    }

    public function getResult(): Money
    {
        return $this->result;
    }

    private function makeOperation(Money $money, string $operation)
    {
        if (!\in_array($operation, get_class_methods(self::class))) {
            throw new \RuntimeException('Incorrect operation specified');
        }

        if ($money->currency !== $this->result->currency) {
            throw new \LogicException('Operation with different currencies is not allowed.');
        }

        $this->result = new Money(
            (new DecimalCalculator($this->result->amount))->$operation($money->amount)->getResult(),
            $this->result->currency,
            $this->result->date
        );

        return $this;
    }
}