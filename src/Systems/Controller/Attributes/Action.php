<?php

namespace Jegulnomic\Systems\Controller\Attributes;

use Attribute;

#[Attribute]
class Action
{
    public function __construct(
        public readonly array $methods,
        public readonly bool $is_index = false
    ) {
    }
}
