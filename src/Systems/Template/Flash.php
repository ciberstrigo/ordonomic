<?php

namespace Jegulnomic\Systems\Template;

class Flash
{
    public const FLASH = 'FLASH_MESSAGES';

    public const FLASH_ERROR = 'error';
    public const FLASH_WARNING = 'warning';
    public const FLASH_INFO = 'info';
    public const FLASH_SUCCESS = 'success';

    public static function createFlash(string $name, string $message, string $type): void
    {
        if (isset($_SESSION[self::FLASH][$name])) {
            unset($_SESSION[self::FLASH][$name]);
        }

        $_SESSION[self::FLASH][$name] = ['message' => $message, 'type' => $type];
    }

    public static function displayFlash($name): string
    {
        if (!isset($_SESSION[self::FLASH][$name])) {
            return '';
        }

        $flash_message = $_SESSION[self::FLASH][$name];
        unset($_SESSION[self::FLASH][$name]);

        return sprintf(
            '<div class="alert alert-%s">%s</div>',
            $flash_message['type'],
            $flash_message['message']
        );
    }

    public static function flash(string $name = '', string $message = '', string $type = ''): ?string
    {
        if ($name !== '' && $message !== '' && $type !== '') {
            self::createFlash($name, $message, $type);
            return null;
        }

        return self::displayFlash($name);
    }
}
