<?php

namespace Jegulnomic\Repository;

use DI\Attribute\Inject;
use Jegulnomic\Entity\RemittanceOperator;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;

class RemittanceOperatorRepository
{
    public function __construct(
        #[Inject(DatabaseStorage::class)]
        private readonly StorageInterface $storage
    )
    {}

    public function getOperator(): ?RemittanceOperator
    {
        return $this->storage->getOne(
            RemittanceOperator::class,
            'WHERE is_verified = 1 AND session_until > UNIX_TIMESTAMP()'
        );
    }
}
