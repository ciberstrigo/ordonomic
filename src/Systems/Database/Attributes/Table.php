<?php

namespace Jegulnomic\Systems\Database\Attributes;
use Attribute;

#[Attribute]
class Table
{
    public function __construct(public readonly string $name)
    {
    }
}