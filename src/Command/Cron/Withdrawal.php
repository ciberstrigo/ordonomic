<?php

namespace Jegulnomic\Command\Cron;

use DI\Attribute\Inject;
use Jegulnomic\Command\AbstractCommand;
use Jegulnomic\Repository\IncomeRepository;
use Jegulnomic\Repository\RemittanceOperatorRepository;
use Jegulnomic\Services\Integration\Telegram\RemittanceOperator\BotProvider;
use Jegulnomic\Services\WithdrawalCreator;
use Jegulnomic\Systems\Command;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

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
        #[Inject(BotProvider::class)]
        private BotProvider $botProvider,
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

        $telegram = $this->botProvider->getBot();
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

        $tgResult = $telegram->sendMessage(
            $message,
            $operator->telegramUserId,
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make(
                        'Я оплатил!',
                        callback_data: sprintf('%s:confirm', $income->withdrawal->id)
                    )
                )
                ->addRow(InlineKeyboardButton::make(
                    'Отмена',
                    callback_data: sprintf('%s:cancel', $income->withdrawal->id)
                ))
        );

        try {
            $income->withdrawal->messageId = $tgResult->message_id;

            $this->storage->save($income);
        } catch (\Throwable $e) {
            $telegram->deleteMessage(
                $income->withdrawal->sentTo->telegramUserId,
                $income->withdrawal->messageId
            );

            throw new \RuntimeException('Error while updating income information '.$e->getMessage());
        }
    }
}
