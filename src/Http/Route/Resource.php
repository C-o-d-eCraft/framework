<?php

namespace Craft\Http\Route;

class Resource
{
    private array $defaultConfig = [
        'index' => [
            'method' => 'GET',
            'action' => 'actionGetList',
            'rules' => [],
            'middlewares' => [],
        ],
        'create' => [
            'method' => 'POST',
            'action' => 'actionCreate',
            'rules' => [],
            'middlewares' => [],
        ],
        'view' => [
            'method' => 'GET',
            'action' => 'actionGetItem',
            'rules' => [
                'id:required|integer',
            ],
            'middlewares' => [],
        ],
        'put' => [
            'method' => 'PUT',
            'action' => 'actionUpdate',
            'rules' => [
                'id:required|integer',
            ],
            'middlewares' => [],
        ],
        'patch' => [
            'method' => 'PATCH',
            'action' => 'actionPatch',
            'rules' => [
                'id:required|integer',
            ],
            'middlewares' => [],
        ],
        'delete' => [
            'method' => 'DELETE',
            'action' => 'actionDelete',
            'rules' => [
                'id:required|integer',
            ],
            'middlewares' => [],
        ],
    ];

    public function __construct(
        public string $path,
        public string $controller,
        public array  $config = []
    ) {
    }

    /**
     * @param RoutesCollection $routes
     * @return void
     */
    public function build(RoutesCollection $routes): void
    {
        $config = array_replace_recursive($this->defaultConfig, $this->config);

        foreach ($config as $action => $settings) {
            $method = $settings['method'];
            $actionPath = $this->path;
            $actionMethod = $settings['action'];
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