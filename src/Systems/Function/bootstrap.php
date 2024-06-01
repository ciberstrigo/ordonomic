<?php

use Jegulnomic\Systems\ContainerProvider;

const SYSTEM_FUNC_DIR = __DIR__;
const VAR_DIR = ENTRYPOINT_DIR . '/../var/';

require_once __DIR__ . '/exception_handler.php';
require_once ENTRYPOINT_DIR . '/../vendor/autoload.php';

// Initializing Dotenv
$environment = file_exists(ENTRYPOINT_DIR . '/../do_not_deploy_me_on_prod') ? 'local' : 'prod';
(Dotenv\Dotenv::createImmutable(ENTRYPOINT_DIR.'/../', '.env.' . $environment))->load();

// Container builder

if ($_ENV['APP_ENV'] === 'prod') {
    $builder = new \DI\ContainerBuilder();
    $builder->enableCompilation(PROJECT_DIR . '/var/tmp');
    $builder->writeProxiesToFile(true, PROJECT_DIR . '/var/tmp/proxies');

    $container = $builder->build();
} else {
    ContainerProvider::buildContainer();
}
