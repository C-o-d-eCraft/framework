<?php

namespace Craft\Contracts;

interface PermissionMiddlewareInterface
{
    public function process(RequestInterface $request, ResponseInterface $response, callable $next, ?array $params): ResponseInterface;
}