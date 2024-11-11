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

    private array $actionsWithId = ['view', 'put', 'patch', 'delete'];

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
            $actionPath = $this->buildActionPath($action, $settings);
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

    /**
     * @param array $middlewares
     * @return $this
     */
    public function addMiddlewaresForAllActions(array $middlewares): self
    {
        foreach ($this->defaultConfig as &$config) {
            foreach ($middlewares as $middleware) {
                $this->addNotExistsMiddleware($config['middlewares'], $middleware);
            }
        }

        return $this;
    }

    /**
     * @param array $middlewares
     * @return self
     */
    public function addIndexMiddlewares(array $middlewares): self
    {
        return $this->addMiddlewareForAction('index', $middlewares);
    }

    /**
     * @param array $middlewares
     * @return self
     */
    public function addCreateMiddlewares(array $middlewares): self
    {
        return $this->addMiddlewareForAction('create', $middlewares);
    }

    /**
     * @param array $middlewares
     * @return self
     */
    public function addViewMiddlewares(array $middlewares): self
    {
        return $this->addMiddlewareForAction('view', $middlewares);
    }

    /**
     * @param array $middlewares
     * @return self
     */
    public function addPutMiddlewares(array $middlewares): self
    {
        return $this->addMiddlewareForAction('put', $middlewares);
    }

    /**
     * @param array $middlewares
     * @return self
     */
    public function addPatchMiddlewares(array $middlewares): self
    {
        return $this->addMiddlewareForAction('patch', $middlewares);
    }

    /**
     * @param array $middlewares
     * @return self
     */
    public function addDeleteMiddlewares(array $middlewares): self
    {
        return $this->addMiddlewareForAction('delete', $middlewares);
    }

    /**
     * @param string $action
     * @param array $middlewares
     * @return self
     */
    private function addMiddlewareForAction(string $action, array $middlewares): self
    {
        foreach ($middlewares as $middleware) {
            $this->addNotExistsMiddleware($this->defaultConfig[$action]['middlewares'], $middleware);
        }

        return $this;
    }

    /**
     * @param string $action
     * @param array $settings
     * @return string
     */
    private function buildActionPath(string $action, array $settings): string
    {
        if (isset($settings['path']) === true) {
            return $settings['path'];
        }

        if (in_array($action, $this->actionsWithId) === true) {
            return $this->path . '/{id}';
        }

        return $this->path;
    }

    /**
     * @param array $middlewares
     * @param string $middleware
     * @return void
     */
    private function addNotExistsMiddleware(array &$middlewares, string $middleware): void
    {
        if (in_array($middleware, $middlewares, true) === false) {
            $middlewares[] = $middleware;
        }
    }
}
