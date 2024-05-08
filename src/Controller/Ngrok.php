<?php

namespace Jegulnomic\Controller;

use Jegulnomic\Systems\PublicUrlProvider;

class Ngrok
{
    public function index()
    {
        PublicUrlProvider::getUrl();
    }
}
