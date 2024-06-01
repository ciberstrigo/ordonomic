<?php

namespace Jegulnomic\Services\Integration\PayPal\TransactionCreator;

use Jegulnomic\ValueObject\Money;

abstract class AbstractTransactionCreator
{
    protected function stringToMoney(string $money, \DateTimeInterface $date): Money
    {
        if (!preg_match('/[A-Z]+$/u', $money, $currencyMatches)) {
            throw new \RuntimeException(sprintf('Invalid $money string. "%s" Currency not found.', $money));
        }

        // ["$30,54","30,54"]
        if (!preg_match('/^.([0-9]+,[0-9]{2})/u', $money, $amountMatches)) {
            throw new \RuntimeException(sprintf('Invalid $money string. "%s" Amount not found.', $money));
        }

        return new Money(
            str_replace(',', '.', $amountMatches[1]),
            $currencyMatches[0],
            $date
        );
    }

    abstract protected function getHeaderExtractionPattern(): string;

    abstract protected function getNameMatchedPosition(): int;

    protected function extractNameFromHeader(string $header): string
    {
        preg_match($this->getHeaderExtractionPattern(), $header, $matches);
        if (count($matches) < $this->getNameMatchedPosition()) {
            throw new \LogicException('Incorrect header parameter received, got: '.$header);
        }

        $from = $matches[$this->getNameMatchedPosition()];

        if (!preg_match('/[\w\d\s]+/', $from)) {
            throw new \LogicException('Incorrect $from value, got: '.$from);
        }

        return trim($from);
    }
}
