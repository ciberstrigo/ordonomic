<?php

namespace Jegulnomic\Controller;

use Jegulnomic\Command\AbstractCommand;
use Jegulnomic\Systems\Command;
use Jegulnomic\Systems\Controller\Attributes\Signature;
use Psr\Container\ContainerInterface;

#[Signature(secret: 'COMMAND_EMULATOR_SECRET')]
class CommandCaller
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    public function index(): void
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

        /** @var AbstractCommand $class */
        $class = $this->container->get($className);
        $class->setArguments($_GET['parameters'] ?? []);
        $class->$methodName();
    }
}
