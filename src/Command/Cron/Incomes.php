<?php

namespace Jegulnomic\Command\Cron;

use Jegulnomic\Repository\IncomeRepository;
use Jegulnomic\Services\Integration\PayPalMailIntegration;
use Jegulnomic\Systems\Database\DatabaseStorage;

class Incomes
{
    public function proceed()
    {
        $incomes = PayPalMailIntegration::getIncomes();
        $newIncomes = IncomeRepository::filterNewIncomes($incomes);
        DatabaseStorage::i()->saveMany($newIncomes);
    }
}
