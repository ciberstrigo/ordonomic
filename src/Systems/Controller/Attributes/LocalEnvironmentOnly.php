<?php

namespace Jegulnomic\Systems\Controller\Attributes;

use Attribute;
use Symfony\Component\HttpFoundation\Response;

#[Attribute]
class LocalEnvironmentOnly
{
    public function __construct()
    {
        if ('local' !== $_ENV['APP_ENV']) {
            http_response_code(Response::HTTP_NOT_FOUND);
            die;
        }
    }
}