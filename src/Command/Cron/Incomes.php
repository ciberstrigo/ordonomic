<?php

namespace Jegulnomic\Command\Cron;

use DI\Attribute\Inject;
use Jegulnomic\Command\AbstractCommand;
use Jegulnomic\Entity\Income;
use Jegulnomic\Repository\IncomeRepository;
use Jegulnomic\Services\BusinessProcess\IncomesGetter;
use Jegulnomic\Services\Integration\PayPal\PayPalMailIntegration;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;

readonly class Incomes extends AbstractCommand
{
    public function __construct(
        #[Inject(IncomesGetter::class)]
        private IncomesGetter $incomesGetter
    ) {
    }

    public function proceed()
    {
        $this->incomesGetter->get();
    }
}
