<?php

namespace Jegulnomic\Command;

class AbstractCommand
{
    public function __construct(protected readonly array $arguments)
    {
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
