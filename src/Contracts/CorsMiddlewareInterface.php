<?php

namespace Craft\Contracts;

interface CorsMiddlewareInterface
{
    /**
     * @param ResponseInterface $response
     * @return void
     */
    public function process(ResponseInterface $response): void;
}
