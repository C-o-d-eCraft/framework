<?php

namespace Craft\Http\Route;

use Craft\Contracts\RoutesCollectionInterface;

class RoutesCollection implements RoutesCollectionInterface
{
    private array $routes = [];
    private array $globalMiddlewares = [];
    private array $groupMiddlewares = [];
    private array $groupPrefixes = [];

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @param string $route
     * @param string|callable $controllerAction
     * @param array $middleware
     * @return void
     */
    public function post(string $route, string|callable $controllerAction, array $middleware = []): void
    {
        $this->addRoute('POST', $route, $controllerAction, $middleware);
    }

    /**
     * @param string $route
     * @param string|callable|array $controllerAction
     * @param array $middleware
     * @return void
     */
    public function get(string $route, string|callable $controllerAction, array $middleware = []): void
    {
        $this->addRoute('GET', $route, $controllerAction, $middleware);
    }

    /**
     * @param string $route
     * @param string|callable $controllerAction
     * @param array $middleware
     * @return void
     */
    public function delete(string $route, string|callable $controllerAction, array $middleware = []): void
    {
        $this->addRoute('DELETE', $route, $controllerAction, $middleware);
    }

    /**
     * @param string $route
     * @param string|callable $controllerAction
     * @param array $middleware
     * @return void
     */
    public function put(string $route, string|callable $controllerAction, array $middleware = []): void
    {
        $this->addRoute('PUT', $route, $controllerAction, $middleware);
    }

    /**
     * @param string $route
     * @param string|callable $controllerAction
     * @param array $middleware
     * @return void
     */
    public function patch(string $route, string|callable $controllerAction, array $middleware = []): void
    {
        $this->addRoute('PATCH', $route, $controllerAction, $middleware);
    }

    /**
     * @param string $prefix
     * @param callable $callback
     * @param array $middleware
     * @return void
     */
    public function group(string $prefix, callable $callback, array $middleware = []): void
    {
        array_push($this->groupMiddlewares, $middleware);
        array_push($this->groupPrefixes, $prefix);

        $callback($this);

        array_pop($this->groupMiddlewares);
        array_pop($this->groupPrefixes);
    }

    /**
     * @param string $prefix
     * @param string $controller
     * @param array $middleware
     * @param array $actionMiddleware
     * @return void
     */
    public function addResource(string $prefix, string $controller, array $middleware = [], array $actionMiddleware = []): void
    {
        $this->get($prefix, $controller . '::actionGetList', $this->mergeActionMiddleware('actionGetList', $middleware, $actionMiddleware));
        $this->post($prefix, $controller . '::actionCreate', $this->mergeActionMiddleware('actionCreate', $middleware, $actionMiddleware));
        $this->get($prefix . '/{id:integer}', $controller . '::actionGetItem', $this->mergeActionMiddleware('actionGetItem', $middleware, $actionMiddleware));
        $this->put($prefix . '/{id:integer}', $controller . '::actionUpdate', $this->mergeActionMiddleware('actionUpdate', $middleware, $actionMiddleware));
        $this->patch($prefix . '/{id:integer}', $controller . '::actionPatch', $this->mergeActionMiddleware('actionPatch', $middleware, $actionMiddleware));
        $this->delete($prefix . '/{id:integer}', $controller . '::actionDelete', $this->mergeActionMiddleware('actionDelete', $middleware, $actionMiddleware));
    }

    /**
     * @param string $method
     * @param string $route
     * @param string|callable $handler
     * @param array $middleware
     * @return void
     */
    private function addRoute(string $method, string $route, string|callable $handler, array $middleware = []): void
    {
        $routePath = $this->applyGroupPrefixes($route);

        $this->routes[] = new Route($method, $routePath, $handler, $this->mergeMiddlewares($middleware));
    }

    /**
     * @param array $middleware
     * @return void
     */
    public function addGlobalMiddleware(array $middleware): void
    {
        $this->globalMiddlewares = array_merge($this->globalMiddlewares, $middleware);
    }

    /**
     * @param array $middlewares
     * @return array
     */
    private function mergeMiddlewares(array $middlewares = []): array
    {
        $allMiddlewares = array_merge($this->globalMiddlewares, ...$this->groupMiddlewares);
        $allMiddlewares = array_merge($allMiddlewares, $middlewares);

        return array_values(array_unique($allMiddlewares));
    }

    /**
     * @param string $route
     * @return string
     */
    private function applyGroupPrefixes(string $route): string
    {
        return '/' . trim(implode('/', array_merge($this->groupPrefixes, [$route])), '/');
    }

    /**
     * @param string $action
     * @param array $middleware
     * @param array $actionMiddleware
     * @return array
     */
    private function mergeActionMiddleware(string $action, array $middleware, array $actionMiddleware): array
    {
        $specificMiddleware = $actionMiddleware[$action] ?? [];

        return $this->mergeMiddlewares(array_merge($middleware, $specificMiddleware));
    }
}
