<?php

namespace Craft\Http\Route;

class Route
{
    /**
     * @param string $method
     * @param string $route
     * @param string|array $controllerAction
     * @param array $params
     * @param array $middlewares
     */
    public function __construct(
        public string       $method,
        public string       $route,
        public string|array $controllerAction,
        public array        $params = [],
        public array        $middlewares = []
    )
    {
    }
}
