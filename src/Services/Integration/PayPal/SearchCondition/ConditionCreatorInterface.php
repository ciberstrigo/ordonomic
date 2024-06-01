<?php

namespace Jegulnomic\Services\Integration\PayPal\SearchCondition;

use Ddeboer\Imap\Search\ConditionInterface;
use Ddeboer\Imap\SearchExpression;

interface ConditionCreatorInterface
{
    public function create(): ConditionInterface;
}
