<?php

namespace Craft\Http\Route;

use Craft\Contracts\RoutesCollectionInterface;
use Craft\Http\Route\DTO\Route;

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
     * @param string $path
     * @param string|callable $handler
     * @param array $rules
     * @param array $middleware
     * @return void
     */
    public function post(string $path, string|callable $handler, array $rules = [], array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $rules, $middleware);
    }

    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $rules
     * @param array $middleware
     * @return void
     */
    public function get(string $path, string|callable $handler, array $rules = [], array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $rules, $middleware);
    }

    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $rules
     * @param array $middleware
     * @return void
     */
    public function delete(string $path, string|callable $handler, array $rules = [], array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $rules, $middleware);
    }

    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $rules
     * @param array $middleware
     * @return void
     */
    public function put(string $path, string|callable $handler, array $rules = [], array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $handler, $rules, $middleware);
    }

    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $rules
     * @param array $middleware
     * @return void
     */
    public function patch(string $path, string|callable $handler, array $rules = [], array $middleware = []): void
    {
        $this->addRoute('PATCH', $path, $handler, $rules, $middleware);
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
     * @param string $path
     * @param string $controller
     * @param array $config
     * @return void
     */
    public function addResource(string $path, string $controller, array $config = []): void
    {
        (new Resource($path, $controller, $config))->build($this);
    }

    /**
     * @param string $method
     * @param string $route
     * @param string|callable $handler
     * @param array $middleware
     * @return void
     */
    public function addRoute(string $method, string $path, string|callable $handler, array $rules = [], array $middleware = []): void
    {
        $routePath = $this->applyGroupPrefixes($path);

        $this->routes[] = new Route($method, $routePath, $handler, $rules, $this->mergeMiddlewares($middleware));
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
