<?php

namespace Jegulnomic\Controller;

use Jegulnomic\Systems\Controller\Attributes\LocalEnvironmentOnly;

#[LocalEnvironmentOnly]
class DevTools
{
    public function phpInfo(): void
    {
        phpinfo();
    }

    public function throw(): void
    {
        throw new \Exception('test exception');
    }
}
