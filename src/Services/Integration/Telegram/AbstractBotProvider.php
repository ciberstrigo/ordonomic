<?php

namespace Jegulnomic\Services\Integration\Telegram;

use SergiX44\Nutgram\Configuration;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;

abstract class AbstractBotProvider
{
    protected ?Nutgram $client = null;

    public function getBot(): Nutgram
    {
        if ($this->client) {
            return $this->client;
        }

        $this->client = new Nutgram(
            $this->getToken(),
            $this->getConfiguration()
        );

        $this->client->setRunningMode(Webhook::class);
        $this->setUpBehaviour($this->client);

        return $this->client;
    }

    protected function getConfiguration(): Configuration
    {
        return new Configuration(
            clientTimeout: 10
        );
    }

    abstract protected function getToken(): string;

    abstract protected function setUpBehaviour(Nutgram $client): void;
}