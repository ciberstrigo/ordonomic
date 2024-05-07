<?php

namespace Jegulnomic\Systems\Template;

class Flash
{
    public const string FLASH = 'FLASH_MESSAGES';

    public const string FLASH_ERROR = 'error';
    public const string FLASH_WARNING = 'warning';
    public const string FLASH_INFO = 'info';
    public const string FLASH_SUCCESS = 'success';

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
