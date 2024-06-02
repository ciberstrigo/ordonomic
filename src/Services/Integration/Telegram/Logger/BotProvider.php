<?php

namespace Jegulnomic\Services\Integration\Telegram\Logger;

use Jegulnomic\Services\Integration\Telegram\AbstractBotProvider;
use Override;
use SergiX44\Nutgram\Nutgram;

class BotProvider extends AbstractBotProvider
{
    #[Override]
    protected function getToken(): string
    {
        return $_ENV['TELEGRAM_LOGGER_BOT_TOKEN'];
    }

    #[Override]
    protected function setUpBehaviour(Nutgram $client): void
    {
        $client->onCommand('start', function (Nutgram $bot) {
            $bot->sendMessage(sprintf('Hello %s', $bot->message()->from->username));
        });

//        $client->onCommand('phpinfo', function (Nutgram $bot) {
//            $message = sprintf('
//                PHP version: %s
//
//                Extensions: %s
//            ',
//                phpversion(),
//                implode(', ', get_loaded_extensions()),
//            );
//
//            $bot->sendMessage($message);
//        });
//
//        $client->onCommand('imap', function (Nutgram $bot) {
//            try {
//                $bot->sendMessage(implode(',', get_extension_funcs('imap')));
//            } catch (\Throwable $exception) {
//                $bot->sendMessage($exception->getMessage());
//            }
//        });
    }
}
