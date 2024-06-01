<?php

namespace Jegulnomic\Services\Integration\PayPal\SearchCondition;

use Ddeboer\Imap\Search\ConditionInterface;
use Ddeboer\Imap\Search\Email\From;
use Ddeboer\Imap\Search\RawExpression;
use Ddeboer\Imap\SearchExpression;
use Override;

class P2PIncomeConditionProvider implements ConditionCreatorInterface
{
    #[Override]
    public function create(): ConditionInterface
    {
        $search = new SearchExpression();
        $search->addCondition(new From($_ENV['RECEIVE_EMAIL_FROM']));
        $search->addCondition(new RawExpression('SUBJECT "You\'ve got money"'));

        return $search;
    }
}
