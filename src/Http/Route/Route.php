<?php

namespace Craft\Http\Route;

class Route
{
    /**
     * @param string $method
     * @param string $route
     * @param string|\Closure $handler
     * @param array $middlewares
     */
    public function __construct(
        public string $method,
        public string $route,
        public string|\Closure $handler,
        public array $middlewares = []
    ) { }
}
