<?php

namespace Craft\Contracts;

interface AuthMiddlewareInterface
{
    /**
     * @param RequestInterface $request
     *
     * @return void
     */
    public function process(RequestInterface $request): void;
}
