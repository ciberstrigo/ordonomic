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
                "Alert!\n"
                . $e->getMessage()
                . "\n"
                . $e->getFile()
                . " on line "
                . $e->getLine()
                . "\n\n"
                . "<code>" . $e->getTraceAsString() . "</code>",
                $_ENV['TELEGRAM_LOGGER_BOT_SEND_TO_ID'],
                parse_mode: ParseMode::HTML
            );
    } catch (Throwable $exception) {
        echo $e->getMessage() . PHP_EOL;
        echo $e->getFile() . PHP_EOL;
        echo $e->getLine() . PHP_EOL;
    }

}
