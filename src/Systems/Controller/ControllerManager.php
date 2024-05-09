<?php

namespace Jegulnomic\Systems\Controller;

class ControllerManager
{
    public function getUrlPath(string $controller): string
    {
        $result = str_replace('Jegulnomic\Controller', '', $controller);
        $result = strtolower(preg_replace('/(?<!^)([A-Z])/', '-$1', str_replace('\\', '/', $result)));
        $result = str_replace('/-', '/', $result);
        return ltrim($result, '-');
    }
}
