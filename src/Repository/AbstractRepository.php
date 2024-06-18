<?php

namespace Jegulnomic\Repository;

use DI\Attribute\Inject;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;

abstract readonly class AbstractRepository
{
    public function __construct(
        #[Inject(DatabaseStorage::class)]
        protected StorageInterface $storage
    ) {
    }

    public function save(array|object $objOrCollection): void
    {
        if (is_array($objOrCollection)) {
            $this->storage->saveMany($objOrCollection);

            return;
        }

        $this->storage->save($objOrCollection);
    }
}
