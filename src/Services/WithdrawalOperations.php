<?php

namespace Jegulnomic\Services;

use Jegulnomic\Entity\Withdrawal;
use Jegulnomic\Enum\Withdrawal\Status;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;

class WithdrawalOperations
{
    private StorageInterface $storage;

    public function __construct()
    {
        $this->storage = DatabaseStorage::i();
    }

    public function confirm(Withdrawal $withdrawal): string
    {
        $withdrawal->status = Status::SUCCESS;
        $this->storage->save($withdrawal);

        return 'Выплата подтверждена оператором. ✅';
    }

    public function cancel(Withdrawal $withdrawal): string
    {
        $withdrawal->status = Status::CANCELED;
        $this->storage->save($withdrawal);

        return 'Запрос на выплату отменен. ❌';
    }
}
