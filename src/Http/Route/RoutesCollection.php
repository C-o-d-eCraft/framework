<?php

namespace Craft\Http\Route;

use Craft\Contracts\MiddlewareInterface;
use Craft\Contracts\RoutesCollectionInterface;
use Craft\Http\Exceptions\NotFoundHttpException;

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
     * @param string|callable $controllerAction
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
     * @param string $name
     * @param string $controller
     * @param array $middleware
     * @return void
     */
    public function addResource(string $prefix, string $controller, array $middleware = []): void
    {
        $this->addRoute('GET', "$prefix", "$controller::actionGet", $middleware);
        $this->addRoute('POST', "$prefix", "$controller::actionPost", $middleware);
        $this->addRoute('GET', "$prefix/{id}", "$controller::actionGetOne", $middleware);
        $this->addRoute('PUT', "$prefix/{id}", "$controller::actionUpdate", $middleware);
        $this->addRoute('DELETE', "$prefix/{id}", "$controller::actionDelete", $middleware);
    }

    /**
     * @param string $method
     * @param string $route
     * @param string|callable $controllerAction
     * @param array $middleware
     * @return void
     */
    private function addRoute(string $method, string $route, string|callable $controllerAction, array $middleware = []): void
    {
        $params = [];

        $routePath = $this->applyGroupPrefixes($route);

        $routeParams = explode('?{', $routePath)[1] ?? '';
        $cleanParams = explode('}{', rtrim($routeParams, '}'));

        foreach ($cleanParams as $param) {
            $parsedParam = $this->parseParams($param);
            $params[] = $parsedParam;
        }

        $this->routes[] = new Route($method, $routePath, $controllerAction, $params, $this->mergeMiddlewares($middleware));
    }

    /**
     * @param string $argument
     * @return array|null
     */
    private function parseParams(string $argument): array|null
    {
        if ($argument === '') {
            return null;
        }

        $param['required'] = true;
        $param['name'] = $argument;
        $param['type'] = 'string';

        if (str_contains($argument, '?')) {
            $param['required'] = false;
            $argument = str_replace('?', '', $argument);
        }

        if (preg_match('/([^|]+)\|type:(\w+)/', $argument, $matches)) {
            $param['name'] = $matches[1];
            $param['type'] = $matches[2];
        }

        if (preg_match('/\|default:(\d+)/', $argument, $defaultMatches)) {
            $param['defaultValue'] = $defaultMatches[1];
        }

        return $param;
    }

    public function addGlobalMiddleware(array $middleware): void
    {
        $this->globalMiddlewares = array_merge($this->globalMiddlewares, $middleware);
    }

    private function mergeMiddlewares(array $middlewares = []): array
    {
        $allMiddlewares = array_merge($this->globalMiddlewares, ...$this->groupMiddlewares);
        $allMiddlewares = array_merge($allMiddlewares, $middlewares);

        return array_values(array_unique($allMiddlewares));
    }

    private function applyGroupPrefixes(string $route): string
    {
        return '/' . trim(implode('/', array_merge($this->groupPrefixes, [$route])), '/');
    }
}
