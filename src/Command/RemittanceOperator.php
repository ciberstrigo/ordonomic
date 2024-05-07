<?php

namespace Jegulnomic\Command;

use Jegulnomic\Entity\RemittanceOperator as Operator;
use Jegulnomic\Services\Integration\Telegram\TelegramIntegration;
use Jegulnomic\Systems\Command;
use Jegulnomic\Systems\Database\DatabaseStorage;

class RemittanceOperator extends AbstractCommand
{
    public function approve(): void
    {
        $id = $this->getArgument(0);

        $storage = DatabaseStorage::i();

        /** @var Operator $operator */
        $operator = $storage->getOne(
            Operator::class,
            'WHERE id = :id',
            [':id' => $id]
        );

        if (!$operator) {
            Command::output(sprintf('Operator with id %s not found.', $id));
            return;
        }

        $operator->isVerified = 1;
        $storage->save($operator);

        Command::output(sprintf('Operator with id %s has been verified.', $id));
        (new TelegramIntegration($_ENV['TELEGRAM_REMITTANCE_OPERATOR_BOT_TOKEN']))
            ->sendMessage([
                'chat_id' => $operator->telegramUserId,
                'text' => 'Ваш аккаунт был подтверждён. Нажмите /start чтобы начать работу.'
            ]);
    }
}
