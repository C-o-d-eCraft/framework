<?php

namespace Craft\Http\Middlewares;

use Craft\Contracts\CorsMiddlewareInterface;
use Craft\Contracts\ResponseInterface;

class CorsMiddleware implements CorsMiddlewareInterface
{
    public function process(ResponseInterface $response): void
    {
        $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->withHeader('Access-Control-Allow-Methods', '*');
        $response->withHeader('Access-Control-Allow-Headers', '*');
        $response->withHeader('Access-Control-Allow-Credentials', 'true');
    }
}
