<?php

namespace Jegulnomic\Controller;

use Jegulnomic\Systems\Command;

class CommandEmulator
{
    public function index()
    {
        if ('DEV' !== $_ENV['APP_ENV']) {
            return;
        }

        $className = '\Jegulnomic\Command\\' . $_GET['class'];
        $methodName = $_GET['method'];

        if (!class_exists($className)) {
            Command::output('No such command class');
            die;
        }

        if (!method_exists($className, $methodName)) {
            Command::output('No such command method');
            die;
        }

        (new $className($_GET['parameters'] ?? []))->$methodName();
    }
}
