<?php

namespace Craft\Contracts;

interface CorsMiddlewareInterface
{
    /**
     * @param ResponseInterface $response
     * @return void
     */
    public function process(RequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface;
}
