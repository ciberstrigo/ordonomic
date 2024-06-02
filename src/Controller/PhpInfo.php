<?php

namespace Jegulnomic\Controller;

class PhpInfo
{
    public function index(): void
    {
        phpinfo();
    }

    public function throw(): void
    {
        throw new \Exception('test exception');
    }
}
