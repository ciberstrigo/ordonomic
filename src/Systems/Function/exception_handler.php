<?php

set_error_handler(
    fn (int $errno, string $errstr, string $errfile = null, int $errline) =>
    !(error_reporting() & $errno) ?: throw new \ErrorException($errstr, 0, $errno, $errfile, $errline)
);
