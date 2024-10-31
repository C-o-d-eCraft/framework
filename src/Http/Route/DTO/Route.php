<?php

namespace Craft\Http\Route\DTO;

class Route
{
    public function __construct(
        public string          $method,
        public string          $path,
        public string|\Closure $handler,
        public array           $rules = [],
        public array           $middlewares = []
    ) {
    }
}