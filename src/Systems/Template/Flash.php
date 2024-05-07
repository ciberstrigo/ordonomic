<?php

namespace Jegulnomic\Systems\Template;

class Flash
{
    const string FLASH = 'FLASH_MESSAGES';

    const string FLASH_ERROR = 'error';
    const string FLASH_WARNING = 'warning';
    const string FLASH_INFO = 'info';
    const string FLASH_SUCCESS = 'success';

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

        return sprintf('<div class="alert alert-%s">%s</div>',
            $flash_message['type'],
            $flash_message['message']
        );
    }

    public static function flash(string $name = '', string $message = '', string $type = '')
    {
        if ($name !== '' && $message !== '' && $type !== '') {
            self::createFlash($name, $message, $type);
        } elseif ($name !== '' && $message === '' && $type === '') {
            return self::displayFlash($name);
        }
    }
}