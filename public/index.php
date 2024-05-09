<?php

const ENTRYPOINT_DIR = __DIR__;

require_once ENTRYPOINT_DIR . '/../src/Systems/Function/bootstrap.php';
//trigger_error("Number cannot be larger than 10");
$requestPath = explode('?', $_SERVER['REQUEST_URI']);
parse_str($requestPath[1] ?? '', $parsed);

(require_once SYSTEM_FUNC_DIR . '/controller_loader.php')($requestPath[0], $parsed);
