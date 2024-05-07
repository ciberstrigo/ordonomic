<?php

namespace Jegulnomic\Repository;

use Jegulnomic\Entity\RemittanceOperator;
use Jegulnomic\Systems\Database\DatabaseStorage;

class RemittanceOperatorRepository
{
    public static function getOperator(): ?RemittanceOperator
    {
        return DatabaseStorage::i()->getOne(
            RemittanceOperator::class,
            'WHERE is_verified = 1 AND session_until > UNIX_TIMESTAMP()'
        );
    }
}