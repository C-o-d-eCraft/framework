<?php

namespace Craft\Http\Middlewares;

use Craft\Contracts\AuthMiddlewareInterface;
use Craft\Contracts\IdentityInterface;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ResponseInterface;
use Craft\Http\Exceptions\UnauthorizedHttpException;

readonly class AuthMiddleware implements AuthMiddlewareInterface
{
    public function __construct(private IdentityInterface $identity) { }

    /**
     * @throws UnauthorizedHttpException
     */
    public function process(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        if (is_int($this->identity->getIdentityUserId()) === false) {
            throw new UnauthorizedHttpException();
        }

        return $next($request, $response);
    }
}
