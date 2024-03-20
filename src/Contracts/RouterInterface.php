<?php

namespace Framework\Contracts;

interface RouterInterface
{
    public function dispatch(RequestInterface $request): ResponseInterface;
}
