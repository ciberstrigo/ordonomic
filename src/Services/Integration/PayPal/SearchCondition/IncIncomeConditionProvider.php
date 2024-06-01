<?php

namespace Jegulnomic\Services\Integration\PayPal\SearchCondition;

use Ddeboer\Imap\Search\ConditionInterface;
use Ddeboer\Imap\Search\Email\From;
use Ddeboer\Imap\Search\Text\Subject;
use Ddeboer\Imap\SearchExpression;

class IncIncomeConditionProvider implements ConditionCreatorInterface
{
    #[\Override]
    public function create(): ConditionInterface
    {
        $search = new SearchExpression();
        $search->addCondition(new From($_ENV['RECEIVE_EMAIL_FROM']));
        $search->addCondition(new Subject('has sent you money'));

        return $search;
    }
}
