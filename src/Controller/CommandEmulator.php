<?php

namespace Jegulnomic\Controller;

use Jegulnomic\Systems\Command;

class CommandEmulator
{
    public function index()
    {
//        if ('DEV' !== $_ENV['APP_ENV']) {
//            return;
//        }

        $className = '\Jegulnomic\Command\\' . $_GET['class'];
        $methodName = $_GET['method'];

        if (!class_exists($className)) {
            Command::output('No such command class ' . $className);
            die;
        }

        if (!method_exists($className, $methodName)) {
            Command::output('No such command method');
            die;
        }

        try {
            (new $className($_GET['parameters'] ?? []))->$methodName();
        } catch(\Throwable $e) {
            echo 'AN Error has been occured ' . PHP_EOL;
            echo $e;
        }

    }
}
