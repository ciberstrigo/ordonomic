<?php

require_once '../vendor/autoload.php';

(Dotenv\Dotenv::createImmutable(__DIR__ . '/..'))->load();

require_once 'controller_loader.php';
require_once 'exception_handler.php';


$requestPath = explode('?', $_SERVER['REQUEST_URI']);
$parsed = []; parse_str($requestPath[1] ?? '', $parsed);
load_controller($requestPath[0], $parsed);