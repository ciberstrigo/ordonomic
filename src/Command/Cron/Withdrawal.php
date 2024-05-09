<?php

namespace Jegulnomic\Command\Cron;

use DI\Attribute\Inject;
use Jegulnomic\Command\AbstractCommand;
use Jegulnomic\Repository\IncomeRepository;
use Jegulnomic\Repository\RemittanceOperatorRepository;
use Jegulnomic\Services\Integration\Telegram\TelegramIntegration;
use Jegulnomic\Services\WithdrawalCreator;
use Jegulnomic\Systems\Command;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;

readonly class Withdrawal extends AbstractCommand
{
    public function __construct(
        #[Inject(DatabaseStorage::class)]
        private StorageInterface $storage,
        #[Inject(IncomeRepository::class)]
        private IncomeRepository $incomeRepository,
        #[Inject(WithdrawalCreator::class)]
        private WithdrawalCreator $withdrawalCreator,
        #[Inject(RemittanceOperatorRepository::class)]
        private RemittanceOperatorRepository $operatorRepository,
        #[Inject(TelegramIntegration::class)]
        private TelegramIntegration $telegramIntegration,
    ) {
    }

    public function createAndNotifyOperator()
    {
        // deps
        $income = $this->incomeRepository->getUnpayedIncome();

        if (null === $income) {
            Command::output('No unpaid incomes.');
            return;
        }

        $telegram = $this->telegramIntegration->setToken($_ENV['TELEGRAM_REMITTANCE_OPERATOR_BOT_TOKEN']);
        $operator = $this->operatorRepository->getOperator();

        if (!$operator) {
            Command::output('No operators at work.');
            return;
        }

        try {
            $income->withdrawal = $this->withdrawalCreator->create($income);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Error while creating withdrawal '.$e->getMessage());
        }

        $income->withdrawal->sentTo = $operator;

        try {
            $this->storage->save($income);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Error while updating income information '.$e->getMessage());
        }

        $message = trim(sprintf(
            "Отправьте %s получателю (жигуль).
        
        Id транзакции: %s 
        Принятая сумма: %s %s 
        От: %s 
        Дата поступления: %s",
            $income->withdrawal->rubbles,
            $income->transactionId,
            $income->amount,
            $income->currency,
            $income->from,
            $income->date->format('d F Y')
        ));

        $tgResult = $telegram->sendMessage([
            'chat_id' => $operator->telegramUserId,
            'text' => $message,
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        [
                            'text' => 'Я оплатил!',
                            'callback_data' => sprintf('%s:confirm', $income->withdrawal->id)
                        ],
                        [
                            'text' => 'Отмена',
                            'callback_data' => sprintf('%s:cancel', $income->withdrawal->id)
                        ],
                    ]
                ],
            ]),
        ]);

        try {
            $income->withdrawal->messageId = $tgResult['result']['message_id'];
            $this->storage->save($income);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Error while updating income information '.$e->getMessage());
        }
    }
}
