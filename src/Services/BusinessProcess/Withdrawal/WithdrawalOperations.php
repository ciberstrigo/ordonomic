<?php

namespace Jegulnomic\Services\BusinessProcess\Withdrawal;

use DI\Attribute\Inject;
use Jegulnomic\Entity\Withdrawal;
use Jegulnomic\Enum\Withdrawal\Status;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;

readonly class WithdrawalOperations
{
    public function __construct(
        #[Inject(DatabaseStorage::class)]
        private StorageInterface $storage
    ) {
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
