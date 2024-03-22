<?php

namespace Craft\Contracts;

interface RouterInterface
{
    /**
     * @return ResponseInterface
     */
    public function dispatch(): ResponseInterface;
}
