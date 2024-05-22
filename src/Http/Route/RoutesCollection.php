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
    private array $groupStack = [];

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
        $this->groupStack[] = $prefix;

        $callback($this);

        $this->groupMiddlewares[$prefix] = $middleware;

        array_pop($this->groupStack);
    }

    /**
     * @param string $name
     * @param string $controller
     * @param array $middleware
     * @return void
     */
    public function addResource(string $route, string|callable $controllerAction, array $middleware = []): void
    {
        $restMethods = ['GET', 'POST', 'PUT', 'DELETE'];

        $controllerClass = is_string($controllerAction) ? explode('::', $controllerAction)[0] : '';

        preg_match('/app\\\\api\\\\(v\d+)\\\\Controllers\\\\(\w+)ResourceController/', $controllerClass, $matches);
        $apiVersion = strtolower($matches[1] ?? '');
        $controllerName = lcfirst($matches[2] ?? '');

        foreach ($restMethods as $method) {
            if (is_callable($controllerAction) || method_exists($controllerAction, 'action' . ucfirst(strtolower($method)))) {
                $path = "/api/{$apiVersion}/{$controllerName}";
                $this->routes[] = new Route($method, $path, $controllerAction . '::action' . ucfirst(strtolower($method)), [], $this->mergeMiddleware($middleware));
            }
        }
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

        $routePath = explode('?{', $route)[0];
        $routeParams = explode('?{', $route)[1] ?? '';

        $cleanParams = explode('}{', rtrim($routeParams, '}'));

        foreach ($cleanParams as $param) {
            $parsedParam = $this->parseParams($param);
            $params[] = $parsedParam;
        }

        $middlewares = $this->mergeMiddleware($middleware);

        $this->routes[] = new Route($method, $routePath, $controllerAction, $params, $middlewares);
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

    private function mergeMiddleware(array $middleware = []): array
    {
        $mergedMiddlewares = [];

        foreach ($this->globalMiddlewares as $globalMiddleware) {
            $mergedMiddlewares[$globalMiddleware] = $globalMiddleware;
        }

        foreach ($this->groupMiddlewares as $groupMiddlewares) {
            foreach ($groupMiddlewares as $groupMiddleware) {
                $mergedMiddlewares[$groupMiddleware] = $groupMiddleware;
            }
        }

        foreach ($middleware as $mw) {
            $mergedMiddlewares[$mw] = $mw;
        }

        return array_values($mergedMiddlewares);
    }

    private function getCurrentGroupPrefix(): string
    {
        return implode('/', $this->groupStack);
    }
}
