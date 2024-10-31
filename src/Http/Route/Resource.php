<?php

namespace Craft\Http\Route;

class Resource
{
    public function __construct(
        public string $path,
        public string $controller,
        public array  $config
    ) {
    }

    /**
     * @param RoutesCollection $routes
     * @return void
     */
    public function build(RoutesCollection $routes): void
    {
        foreach ($this->config as $action => $settings) {
            $method = $settings['method'] ?? 'GET';
            $actionPath = $settings['path'] ?? $this->path;
            $actionMethod = $settings['action'] ?? 'actionIndex';
            $handler = "{$this->controller}::{$actionMethod}";

            $routes->addRoute(
                $method,
                $actionPath,
                $handler,
                $settings['rules'] ?? [],
                $settings['middlewares'] ?? []
            );
        }
    }
}