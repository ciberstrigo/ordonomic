<?php

namespace Jegulnomic\Command\Cron;

use Jegulnomic\Repository\IncomeRepository;
use Jegulnomic\Repository\RemittanceOperatorRepository;
use Jegulnomic\Services\Integration\Telegram\TelegramIntegration;
use Jegulnomic\Services\WithdrawalCreator;
use Jegulnomic\Systems\Command;
use Jegulnomic\Systems\Database\DatabaseStorage;

class Withdrawal
{
    public function createAndNotifyOperator()
    {
        // deps
        $income = IncomeRepository::getUnpayedIncome();

        if (null === $income) {
            Command::output('No unpaid incomes.');
            return;
        }

        $withdrawalCreator = new WithdrawalCreator();
        $telegram = new TelegramIntegration($_ENV['TELEGRAM_REMITTANCE_OPERATOR_BOT_TOKEN']);
        $operator = RemittanceOperatorRepository::getOperator();
        $databaseStorage = DatabaseStorage::i();

        if (!$operator) {
            Command::output('No operators at work.');
            return;
        }

        try {
            $income->withdrawal = $withdrawalCreator->create($income);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Error while creating withdrawal '.$e->getMessage());
        }

        $income->withdrawal->sentTo = $operator;

        try {
            $databaseStorage->save($income);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Error while updating income information '.$e->getMessage());
        }

        $message = trim(sprintf("Отправьте %s получателю (жигуль).
        
        Id транзакции: %s 
        Принятая сумма: %s %s 
        От: %s 
        Дата поступления: %s", $income->withdrawal->rubbles,
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
            $databaseStorage->save($income);
        } catch (\Throwable $e) {
            throw new \RuntimeException('Error while updating income information '.$e->getMessage());
        }
    }
}