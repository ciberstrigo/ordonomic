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

ContainerProvider::buildContainer();

