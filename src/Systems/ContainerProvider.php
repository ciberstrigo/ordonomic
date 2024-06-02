<?php

namespace Jegulnomic\Systems;

use DI\Container;
use Psr\Container\ContainerInterface;

class ContainerProvider
{
    private static ContainerInterface $container;

    public static function buildContainer()
    {
        $builder = new \DI\ContainerBuilder();

        //        if ('prod' === $_ENV['APP_ENV']) {
        //            $builder->enableCompilation(PROJECT_DIR . '/var/tmp');
        //            $builder->writeProxiesToFile(true, PROJECT_DIR . '/var/tmp/proxies');
        //        }

        $builder->useAutowiring(false);
        $builder->useAttributes(true);


        self::$container = $builder->build();
    }

    public static function getContainer(): ContainerInterface
    {
        return self::$container;
    }
}
