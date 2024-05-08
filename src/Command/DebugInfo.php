<?php

namespace Jegulnomic\Command;

use Jegulnomic\Systems\Command;

class DebugInfo
{
    public function httpHost()
    {
        Command::output(print_r($_SERVER));
    }
}
