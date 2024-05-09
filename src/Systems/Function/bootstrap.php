<?php

use Jegulnomic\Systems\ContainerProvider;

const SYSTEM_FUNC_DIR = __DIR__;
const VAR_DIR = ENTRYPOINT_DIR . '/../var/';

require_once __DIR__ . '/exception_handler.php';
require_once ENTRYPOINT_DIR . '/../vendor/autoload.php';

// Initializing Dotenv
(Dotenv\Dotenv::createImmutable(ENTRYPOINT_DIR.'/../'))->load();

// Container builder
ContainerProvider::buildContainer();
