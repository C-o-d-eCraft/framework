<?php

namespace Craft\Contracts;

interface OptionsMiddlewareInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return void
     */
    public function process(RequestInterface $request, ResponseInterface $response): void;
}
