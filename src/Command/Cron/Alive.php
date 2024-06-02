<?php

namespace Jegulnomic\Command\Cron;

use Jegulnomic\Command\AbstractCommand;
use Jegulnomic\Services\Integration\Telegram\Logger\BotProvider;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

readonly class Alive extends AbstractCommand
{
    public function notice(): void
    {
        $bot = (new BotProvider())->getBot();

        $bot
            ->sendMessage(
                'I\'m alive!',
                $_ENV['TELEGRAM_LOGGER_BOT_SEND_TO_ID'],
                parse_mode: ParseMode::HTML
            );
    }
}