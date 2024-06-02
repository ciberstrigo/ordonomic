<?php

namespace Jegulnomic\Command;

use Jegulnomic\Services\Integration\Telegram\Logger\BotProvider;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class Alive extends AbstractCommand
{
    public function notice(): void
    {
        $bot = (new BotProvider())->getBot();

        $s = 'Hey man look i' . PHP_EOL;
        $s .= 'I\'m alive!';


        $bot
            ->sendMessage(
                $s,
                $_ENV['TELEGRAM_LOGGER_BOT_SEND_TO_ID'],
                parse_mode: ParseMode::HTML
            );
    }
}
