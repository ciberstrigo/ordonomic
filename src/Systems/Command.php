<?php

namespace Jegulnomic\Systems;

class Command
{
    public static function input(?string $invitation = '', ?string $default = ''): string
    {
        echo(!empty($invitation) ? $invitation.' ' : '');
        $input = trim(fgets(STDIN));

        if (empty($input)) {
            return $default;
        }

        return $input;
    }

    public static function output(string $text): void
    {
        echo($text . PHP_EOL);
    }
}