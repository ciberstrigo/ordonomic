<?php

namespace Jegulnomic\Command\Cron;

use DI\Attribute\Inject;
use Jegulnomic\Command\AbstractCommand;
use Jegulnomic\Repository\IncomeRepository;
use Jegulnomic\Services\Integration\PayPalMailIntegration;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;

readonly class Incomes extends AbstractCommand
{
    public function __construct(
        #[Inject(DatabaseStorage::class)]
        private StorageInterface $storage,
        #[Inject(PayPalMailIntegration::class)]
        private PayPalMailIntegration $payPalMailIntegration,
        #[Inject(IncomeRepository::class)]
        private IncomeRepository $incomeRepository,
    ) {
    }

    public function proceed()
    {
        $incomes = $this->payPalMailIntegration->getIncomes();
        $newIncomes = $this->incomeRepository->filterNewIncomes($incomes);
        $this->storage->saveMany($newIncomes);
    }
}
