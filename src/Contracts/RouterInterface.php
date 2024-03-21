<?php

namespace Craft\Contracts;

interface RouterInterface
{
    public function dispatch(RequestInterface $request): ResponseInterface;
}
