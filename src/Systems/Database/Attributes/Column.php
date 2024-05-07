<?php

namespace Jegulnomic\Systems\Database\Attributes;

use Attribute;

#[Attribute]
class Column
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $relatedTo = null
    ) {
    }
}
