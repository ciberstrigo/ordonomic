<?php

namespace Jegulnomic\Command\Cron;

use DI\Attribute\Inject;
use Jegulnomic\Command\AbstractCommand;
use Jegulnomic\Entity\Income;
use Jegulnomic\Repository\IncomeRepository;
use Jegulnomic\Services\Integration\PayPal\PayPalMailIntegration;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;

readonly class Incomes extends AbstractCommand
{
    public function __construct(
        #[Inject(PayPalMailIntegration::class)]
        private PayPalMailIntegration $payPalMailIntegration,
        #[Inject(IncomeRepository::class)]
        private IncomeRepository $incomeRepository,
    ) {
    }

    public function proceed()
    {
        $incomes = $this->payPalMailIntegration->connect()->getAllIncomes();

        $incomes = array_map(
            fn ($transaction) => Income::createFromPayPalTransaction($transaction),
            $incomes
        );

        $newIncomes = $this->incomeRepository->filterNewIncomes($incomes);

        $this->incomeRepository->save($newIncomes);
    }
}
