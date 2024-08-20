<?php

namespace Craft\Http\Middlewares;

use Craft\Contracts\AuthMiddlewareInterface;
use Craft\Contracts\RequestInterface;
use Craft\Http\Exceptions\NotAuthorizedHttpException;

class AuthMiddleware implements AuthMiddlewareInterface
{
    public function __construct(private string $headerName = 'X-BASE-AUTH') { }

    public function process(RequestInterface $request): void
    {
        $authHeaderValue = $request->getHeaders()[$this->headerName] ?? null;

        if ($authHeaderValue === null) {
            throw new NotAuthorizedHttpException();
        }
    }
}
