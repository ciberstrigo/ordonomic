<?php

namespace Jegulnomic\Services\Integration\Telegram\RemittanceOperator;

use DI\Attribute\Inject;
use Jegulnomic\Entity\Withdrawal;
use Jegulnomic\Services\Integration\Telegram\AbstractBotProvider;
use Jegulnomic\Services\Integration\Telegram\RemittanceOperator\Commands\StartCommand;
use Jegulnomic\Services\WithdrawalOperations;
use Jegulnomic\Systems\Database\DatabaseStorage;
use Jegulnomic\Systems\StorageInterface;
use Override;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class BotProvider extends AbstractBotProvider
{
    public function __construct(
        #[Inject(DatabaseStorage::class)]
        private readonly StorageInterface $storage,
        #[Inject(WithdrawalOperations::class)]
        private readonly WithdrawalOperations $withdrawalOperations
    ) {
    }

    #[Override]
    protected function getToken(): string
    {
        return $_ENV['TELEGRAM_REMITTANCE_OPERATOR_BOT_TOKEN'];
    }

    #[Override]
    protected function setUpBehaviour(Nutgram $client): void
    {
        $client->registerCommand(StartCommand::class);
        $client->onCallbackQueryData('{id}:{operation}', function (Nutgram $bot, $id, $operation) {
            $getResponse = function ($id, $operation): string {
                try {
                    $withdrawal = $this->storage->getOne(
                        Withdrawal::class,
                        'WHERE id = :id',
                        [':id' => $id]
                    );

                    if (null === $withdrawal) {
                        return 'Ошибка. Выплата не найдена.';
                    }

                    if (!method_exists($this->withdrawalOperations, $operation)) {
                        return 'Ошибка. Неизвестная операция.';
                    }

                    return $this->withdrawalOperations->$operation($withdrawal);
                } catch (\Throwable $exception) {
                    return 'Ошибка в системе.';
                }
            };

            $bot->editMessageText(
                $bot->callbackQuery()->message->text. "\n\n" . $getResponse($id, $operation),
                $bot->callbackQuery()->message->chat->id,
                $bot->callbackQuery()->message->message_id,
                reply_markup: (new InlineKeyboardMarkup())
            );
        });
    }
}
