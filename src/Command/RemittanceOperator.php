<?php

namespace Jegulnomic\Command;

use DI\Attribute\Inject;
use Jegulnomic\Entity\RemittanceOperator as Operator;
use Jegulnomic\Services\Integration\Telegram\TelegramIntegration;
use Jegulnomic\Systems\Command;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;

readonly class RemittanceOperator extends AbstractCommand
{
    public function __construct(
        #[Inject(DatabaseStorage::class)]
        private StorageInterface $storage,
        #[Inject(TelegramIntegration::class)]
        private TelegramIntegration $telegramIntegration
    ) {
    }

    public function approve(): void
    {
        $id = $this->getArgument(0);

        /** @var Operator $operator */
        $operator = $this->storage->getOne(
            Operator::class,
            'WHERE id = :id',
            [':id' => $id]
        );

        if (!$operator) {
            Command::output(sprintf('Operator with id %s not found.', $id));
            return;
        }

        $operator->isVerified = 1;
        $this->storage->save($operator);

        Command::output(sprintf('Operator with id %s has been verified.', $id));

        $this->telegramIntegration->setToken($_ENV['TELEGRAM_REMITTANCE_OPERATOR_BOT_TOKEN'])
            ->sendMessage([
                'chat_id' => $operator->telegramUserId,
                'text' => 'Ваш аккаунт был подтверждён. Нажмите /start чтобы начать работу.'
            ]);
    }
}
