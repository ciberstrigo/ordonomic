<?php

namespace Jegulnomic\Controller;

use Jegulnomic\Command\AbstractCommand;
use Jegulnomic\Systems\Command;
use Psr\Container\ContainerInterface;

class CommandEmulator
{
    public function __construct(
        private ContainerInterface $container
    ) {
    }

    public function index()
    {
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
            /** @var AbstractCommand $class */
            $class = $this->container->get($className);
            $class->setArguments($_GET['parameters'] ?? []);
            $class->$methodName();
        } catch(\Throwable $e) {
            echo 'AN Error has been occured ' . PHP_EOL;
            echo $e;
        }

    }
}
