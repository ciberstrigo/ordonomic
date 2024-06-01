<?php

namespace Jegulnomic\Services\Integration\PayPal\TransactionCreator;

use Jegulnomic\Services\Integration\PayPal\Transactions\TransactionInterface;

interface TransactionCreatorInterface
{
    public function create(
        string $header,
        string $id,
        string $date,
        string $amount,
        string $fee,
        string $total
    ): TransactionInterface;
}
