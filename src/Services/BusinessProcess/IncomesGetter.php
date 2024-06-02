<?php

namespace Jegulnomic\Services\BusinessProcess;

use DI\Attribute\Inject;
use Jegulnomic\Entity\Income;
use Jegulnomic\Repository\IncomeRepository;
use Jegulnomic\Services\Integration\PayPal\PayPalMailIntegration;

readonly class IncomesGetter
{
    public function __construct(
        #[Inject(PayPalMailIntegration::class)]
        private PayPalMailIntegration $payPalMailIntegration,
        #[Inject(IncomeRepository::class)]
        private IncomeRepository $incomeRepository,
    ) {
    }

    public function get(): void
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
