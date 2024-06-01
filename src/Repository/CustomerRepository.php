<?php

namespace Jegulnomic\Repository;

use DI\Attribute\Inject;
use Jegulnomic\Entity\Customer;
use Jegulnomic\Entity\RemittanceOperator;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;

readonly class CustomerRepository
{
    public function __construct(
        #[Inject(DatabaseStorage::class)]
        private StorageInterface $storage
    ) {
    }

    public function getCustomerById(string $id): ?RemittanceOperator
    {
        return $this->storage->getOne(
            Customer::class,
            'WHERE id = :id',
            [':id' => $id]
        );
    }
}
