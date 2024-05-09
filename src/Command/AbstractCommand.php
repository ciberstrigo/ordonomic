<?php

namespace Jegulnomic\Command;

use Jegulnomic\Systems\StorageInterface;

class AbstractCommand
{
    private array $arguments = [];

    public function setArguments(array $arguments): self
    {
        if (!empty($this->arguments)) {
            throw new \LogicException('This is not possible to set command arguments many times.');
        }

        $this->arguments = $arguments;

        return $this;
    }

    public function getArgument(int $index): string
    {
        if (!array_key_exists($index, $this->arguments)) {
            throw new \RuntimeException(
                sprintf(
                    'Argument %d not found in %s',
                    $index,
                    get_class($this)
                )
            );
        }

        return $this->arguments[$index];
    }
}
