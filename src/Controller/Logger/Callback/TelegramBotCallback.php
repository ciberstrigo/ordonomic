<?php

namespace Jegulnomic\Controller\Logger\Callback;

use DI\Attribute\Inject;
use Jegulnomic\Services\Integration\Telegram\AbstractBotProvider;
use Jegulnomic\Services\Integration\Telegram\Logger\BotProvider;

class TelegramBotCallback
{
    public function __construct(
        #[Inject(BotProvider::class)]
        private AbstractBotProvider $telegram
    ) {
    }

    public function index(): void
    {
        $bot = $this->telegram->getBot();

        $bot->run();
    }
}
