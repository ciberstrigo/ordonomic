<?php

namespace Jegulnomic\Controller\Customer\Callback;

use DI\Attribute\Inject;
use Jegulnomic\Services\Integration\Telegram\Customer\BotProvider;

readonly class TelegramBotCallback
{
    public function __construct(
        #[Inject(BotProvider::class)]
        private BotProvider $telegram
    ) {
    }

    public function index(): void
    {
        $bot = $this->telegram->getBot();

        $bot->run();
    }
}
