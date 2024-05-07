<?php

namespace Jegulnomic\DTO\Interfaces;

interface RateContainerInterface
{
    public function getRate(): string;

    public function getDiff(): string;
}
