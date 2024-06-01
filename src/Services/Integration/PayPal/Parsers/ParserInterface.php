<?php

namespace Jegulnomic\Services\Integration\PayPal\Parsers;

use Ddeboer\Imap\MessageInterface;
use Jegulnomic\Services\Integration\PayPal\Transactions\TransactionInterface;

interface ParserInterface
{
    public function parse(MessageInterface $message): TransactionInterface;
}
