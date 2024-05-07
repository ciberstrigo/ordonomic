<?php

function exception_error_handler(int $errno, string $errstr, string $errfile = null, int $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }
    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler(exception_error_handler(...));
