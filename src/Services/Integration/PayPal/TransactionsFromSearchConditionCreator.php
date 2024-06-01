<?php

namespace Jegulnomic\Services\Integration\PayPal;

use DateInterval;
use DateTimeImmutable;
use Ddeboer\Imap\MailboxInterface;
use Ddeboer\Imap\Search\ConditionInterface;
use Ddeboer\Imap\Search\Email\From;
use Ddeboer\Imap\Search\Date\Since;
use Ddeboer\Imap\SearchExpression;
use Jegulnomic\Services\Integration\PayPal\Parsers\ParserInterface;

readonly class TransactionsFromSearchConditionCreator
{
    private const DAYS_AGO = 10;

    public function getTransactionsByCondition(
        MailboxInterface $mailbox,
        ConditionInterface $condition,
        ParserInterface $parser,
    ): array {
        $search = new SearchExpression();
        $search->addCondition(new From($_ENV['RECEIVE_EMAIL_FROM']));

        $search->addCondition(new Since(
            (new DateTimeImmutable())
                ->sub(new DateInterval('P'.self::DAYS_AGO.'D'))
        ));
        $search->addCondition($condition);

        $messages = $mailbox->getMessages(
            $search,
            sortCriteria: SORTDATE,
        );

        $result = [];

        foreach ($messages as $message) {
            $result[] = $parser->parse($message);
        }

        return $result;
    }
}
