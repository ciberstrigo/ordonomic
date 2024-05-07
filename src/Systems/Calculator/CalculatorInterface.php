<?php

namespace Jegulnomic\Systems\Calculator;

interface CalculatorInterface
{
    public function __construct(string $decimal);

    public function add(string $decimal): self;

    public function subtract(string $decimal): self;

    public function divide(string $decimal): self;

    public function multiply(string $decimal): self;

    public function getResult(): string;
}