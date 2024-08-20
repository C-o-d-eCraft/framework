<?php

namespace Craft\Http\Middlewares;

use Craft\Contracts\MiddlewareInterface;
use Craft\Contracts\ResponseInterface;

class CorsMiddleware
{
    public function process(ResponseInterface $response): void
    {
        $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->withHeader('Access-Control-Allow-Methods', '*');
        $response->withHeader('Access-Control-Allow-Headers', '*');
        $response->withHeader('Access-Control-Allow-Credentials', 'true');
    }
}