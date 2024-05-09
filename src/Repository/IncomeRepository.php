<?php

namespace Jegulnomic\Repository;

use DI\Attribute\Inject;
use Jegulnomic\Entity\Income;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;

readonly class IncomeRepository
{
    public function __construct(
        #[Inject(DatabaseStorage::class)]
        private StorageInterface $storage
    ) {
    }

    public function filterNewIncomes(array $incomes): array
    {
        if (empty($incomes)) {
            return [];
        }

        $pdo = $this->storage->getPDO();

        $statement = $pdo->prepare(
            sprintf(
                'SELECT transaction_id from income WHERE transaction_id in (%s)',
                implode(
                    ',',
                    array_fill(0, count($incomes), '?')
                )
            )
        );

        foreach (array_map(fn ($income) => $income->transactionId, $incomes) as $key => $id) {
            $statement->bindValue(($key + 1), $id);
        }

        $statement->execute();

        while ($duplicatedIncomeTransactionId = $statement->fetchColumn()) {
            foreach ($incomes as $key => $income) {
                if ($income->transactionId === $duplicatedIncomeTransactionId) {
                    unset($incomes[$key]);
                    break;
                }
            }
        }

        return $incomes;
    }

    public function getUnpayedIncome(): ?Income
    {
        return $this->storage->getOne(Income::class, 'WHERE withdrawal_id is NULL');
    }
}
