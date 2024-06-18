<?php

namespace Jegulnomic\Entity;

interface UserInterface
{
    public static function register(int $telegramId, int $sessionUntil, string $passwordHashed): self;

    public function endSession(): void;

    public function isAllowToProceed(): bool;
}
