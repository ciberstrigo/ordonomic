<?php

namespace Jegulnomic\Repository;

use Jegulnomic\Entity\Income;
use Jegulnomic\Systems\Database\DatabaseStorage;

class IncomeRepository
{
    public static function filterNewIncomes(array $incomes): array
    {
        if (empty($incomes)) {
            return [];
        }

        $pdo = DatabaseStorage::i()->getPDO();

        $statement = $pdo->prepare(
            sprintf(
            'SELECT transaction_id from income WHERE transaction_id in (%s)'
            , implode(
                ',',
                array_fill(0, count($incomes), '?')
            ))
        );

        foreach (array_map(fn($income) => $income->transactionId, $incomes) as $key => $id) {
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

    public static function getUnpayedIncome(): ?Income
    {
        return DatabaseStorage::i()->getOne(Income::class, 'WHERE withdrawal_id is NULL');
    }
}