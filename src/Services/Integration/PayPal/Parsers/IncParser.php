<?php

namespace Jegulnomic\Services\Integration\PayPal\Parsers;

use Ddeboer\Imap\MessageInterface;
use Jegulnomic\Services\Integration\PayPal\TransactionCreator\TransactionCreatorInterface;
use Jegulnomic\Services\Integration\PayPal\Transactions\TransactionInterface;
use pQuery;

readonly class IncParser implements ParserInterface
{
    public function __construct(
        private TransactionCreatorInterface $transactionCreator
    ) {
    }

    public function parse(MessageInterface $message): TransactionInterface
    {
        $html = $message->getCompleteBodyHtml();
        $dom = pQuery::parseStr($html);
        $base = $dom->query('table.ppsans[dir=ltr]')->html();
        $baseNode = pQuery::parseStr($base);

        $from = $baseNode->select('table', 0)->text();
        $transactionIdAndDate = pQuery::parseStr($baseNode->select('table', 2)->html());

        $transactionId = $transactionIdAndDate->select('td', 0)->select('a span', 0)->text();
        $transactionDate = $transactionIdAndDate->select('td', 1)->select('span', 1)->text();

        $transactionAmount = $baseNode->select('table', 6)->select('td', 1)->text();

        return $this->transactionCreator->create(
            $from,
            $transactionId,
            $transactionDate,
            $transactionAmount,
            'EMPTY',
            'EMPTY'
        );
    }
}
