<?php

namespace Craft\Http\Middlewares;

use Craft\Contracts\AuthMiddlewareInterface;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ResponseInterface;
use Craft\Http\Exceptions\NotAuthorizedHttpException;

class AuthMiddleware implements AuthMiddlewareInterface
{
    public function __construct(private string $headerName = 'X-BASE-AUTH') { }

    public function process(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        $authHeaderValue = $request->getHeaders()[$this->headerName] ?? null;

        if ($authHeaderValue === null) {
            throw new NotAuthorizedHttpException();
        }

        return $next($request, $response);
    }
}
