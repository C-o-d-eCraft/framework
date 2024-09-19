<?php

namespace Craft\Contracts;

interface CustomMiddleware
{
    public function process(RequestInterface $request, ResponseInterface $response, callable $next, ?array $params): ResponseInterface;
}