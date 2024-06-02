<?php

namespace Jegulnomic\Controller;

use Jegulnomic\Systems\Template\Template;

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

    public function shell()
    {
        return 'disabled';
    }
}
