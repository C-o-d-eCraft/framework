<?php

namespace Craft\Contracts;

interface MiddlewareInterface
{
    /**
     * @param RequestInterface $request
     * @return void
     */
    public function process(RequestInterface $request): void;
}
