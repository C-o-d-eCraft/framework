<?php

namespace Craft\Contracts;

interface ErrorMiddlewareInterface
{
    public function process(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface;
}