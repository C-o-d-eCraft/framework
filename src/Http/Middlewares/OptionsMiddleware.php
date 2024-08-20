<?php

namespace Craft\Http\Middlewares;

use Craft\Contracts\OptionsMiddlewareInterface;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ResponseInterface;

class OptionsMiddleware implements OptionsMiddlewareInterface
{
    public function process(RequestInterface $request, ResponseInterface $response): void
    {
        if ($request->getMethod() === 'OPTIONS') {
            $response->withStatus(200);
        }
    }
}
