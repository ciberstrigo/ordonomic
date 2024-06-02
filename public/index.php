<?php

use Jegulnomic\Services\Integration\Telegram\Logger\BotProvider;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

const ENTRYPOINT_DIR = __DIR__;
define('PROJECT_DIR', str_replace('/public', '', ENTRYPOINT_DIR));

require_once ENTRYPOINT_DIR . '/../src/Systems/Function/bootstrap.php';
//trigger_error("Number cannot be larger than 10");
$requestPath = explode('?', $_SERVER['REQUEST_URI']);
parse_str($requestPath[1] ?? '', $parsed);

try {
    (require_once SYSTEM_FUNC_DIR.'/controller_loader.php')($requestPath[0], $parsed);
} catch (\Throwable $e) {
    $bot = (new BotProvider())->getBot();

    try {
        $bot
            ->sendMessage(
                mb_strimwidth(
                    "Alert!\n"
                    . $e->getMessage()
                    . "\n"
                    . $e->getFile()
                    . " on line "
                    . $e->getLine()
                    . "\n\n"
                    . "<code>" . $e->getTraceAsString() . "</code>",
                    0,
                    1024
                ),
                $_ENV['TELEGRAM_LOGGER_BOT_SEND_TO_ID'],
                parse_mode: ParseMode::HTML
            );
    } catch (Throwable $exception) {
        echo 'Unsent to bot exception: <br>';
        echo $e->getMessage() . '<br>';
        echo $e->getFile() . '<br>';
        echo $e->getLine() . '<br>';
        echo '<br>';
        echo 'Reason of fail telegram notification <br>';
        echo $exception->getMessage() . '<br>';
        echo $exception->getFile() . '<br>';
        echo $exception->getLine() . '<br>';
    }

}
