<?php

namespace Jegulnomic\Controller;

class CommandEmulator
{
    public function index()
    {
        $className = '\Jegulnomic\Command\\' . $_GET['class'];
        $methodName = $_GET['method'];

        if (!class_exists($className)) {
            \Jegulnomic\Systems\Command::output('No such command class');
            die;
        }

        if (!method_exists($className, $methodName)) {
            \Jegulnomic\Systems\Command::output('No such command method');
            die;
        }

        (new $className($_GET['parameters'] ?? []))->$methodName();
    }
}
