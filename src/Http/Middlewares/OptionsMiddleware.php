<?php

namespace Craft\Http\Middlewares;

use Craft\Contracts\OptionsMiddlewareInterface;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ResponseInterface;

class OptionsMiddleware implements OptionsMiddlewareInterface
{
    public function process(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        if ($request->getMethod() === 'PUT') {
            return $response->withStatus(200);
        }

        return $next($request, $response);
    }
}
