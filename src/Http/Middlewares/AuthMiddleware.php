<?php

namespace Craft\Http\Middlewares;

use Craft\Contracts\MiddlewareInterface;
use Craft\Contracts\RequestInterface;
use Craft\Http\Exceptions\NotAuthorizedHttpException;

class AuthMiddleware implements MiddlewareInterface
{
    public function process(RequestInterface $request): void
    {
        $authHeader = $request->getHeaders()['X-BASE-AUTH'] ?? null;

        if ($authHeaderValue === null) {
            throw new NotAuthorizedHttpException();
        }
    }
}