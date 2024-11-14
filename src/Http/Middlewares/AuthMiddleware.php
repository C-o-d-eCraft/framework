<?php

namespace Craft\Http\Middlewares;

use Craft\Contracts\AuthMiddlewareInterface;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ResponseInterface;
use Craft\Http\Exceptions\UnauthorizedHttpException;

readonly class AuthMiddleware implements AuthMiddlewareInterface
{
    /**
     * @throws UnauthorizedHttpException
     */
    public function process(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        $jwt = $request->getHeaderLine('X-BASE-AUTH');

        if (isset($jwt) === false) {
            throw new UnauthorizedHttpException();
        }

        return $next($request, $response);
    }
}
