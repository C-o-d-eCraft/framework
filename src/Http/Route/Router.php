<?php

namespace Craft\Http\Route;

use Craft\Components\DIContainer\DIContainer;
use Craft\Contracts\CorsMiddlewareInterface;
use Craft\Contracts\OptionsMiddlewareInterface;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ResponseInterface;
use Craft\Contracts\RouterInterface;
use Craft\Contracts\RoutesCollectionInterface;
use Craft\Http\Exceptions\BadRequestHttpException;
use Craft\Http\Exceptions\HttpException;
use Craft\Http\Exceptions\NotFoundHttpException;
use ReflectionException;

readonly class Router implements RouterInterface
{
    /**
     * @param DIContainer $container
     * @param RoutesCollectionInterface $routesCollection
     * @param MiddlewareInterface $middleware
     * @param RequestInterface $request
     */
    public function __construct(
        private DIContainer               $container,
        private RoutesCollectionInterface $routesCollection,
        private RequestInterface          $request,
        private ResponseInterface $response
    ) { }

    /**
     * @return ResponseInterface
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws ReflectionException
     */
    public function dispatch(): ResponseInterface
    {
        $method = $this->request->getMethod();
        $path = $this->request->getUri()->getPath();

        foreach ($this->routesCollection->getRoutes() as $route) {
            $params = [];

            if ($this->handleRoute($route, $path, $method, $params)) {
                $this->processMiddlewares($route->middlewares);

                [$controllerNameSpace, $action] = explode('::', $route->handler);

                return $this->container->call($controllerNameSpace, $action, $params);
            }
        }

        throw new NotFoundHttpException("Страница не найдена для $method запроса по пути $path");
    }

    /**
     * @param Route $route
     * @param string $path
     * @param string $method
     * @param array $params
     * @return bool
     */
    private function handleRoute(Route $route, string $path, string $method, array &$params = []): bool
    {
        $routePattern = $this->buildRoutePattern($route->route);

        if (preg_match($routePattern, $path, $matches) && $route->method === $method) {
            $params = $this->extractParams($route->route, $matches);

            return true;
        }

        return false;
    }

    /**
     * @param string $route
     * @return string
     */
    private function buildRoutePattern(string $route): string
    {
        return '@^' . preg_replace_callback('/\{(\w+)(:[^}]+)?\}/', function ($matches) {
                $paramPattern = $matches[2] ?? '';

                return '(' . ($this->convertParamPattern($paramPattern) ?: '\w+') . ')';

            }, $route) . '$@';
    }

    /**
     * @param string $pattern
     * @return string
     */
    private function convertParamPattern(string $pattern): string
    {
        $specifiers = explode('|', trim($pattern, ':'));

        $regexParts = array_map(function ($specifier) {
            return match ($specifier) {
                'integer' => '\d+',
                'required' => '.+',
                default => '\w+',
            };
        }, $specifiers);

        return implode('|', $regexParts);
    }

    /**
     * @param string $route
     * @param array $matches
     * @return array
     * @throws BadRequestHttpException
     */
    private function extractParams(string $route, array $matches): array
    {
        preg_match_all('/\{(\w+)(:[^}]+)?\}/', $route, $paramNames);

        $params = [];
        foreach ($paramNames[1] as $index => $name) {
            $paramSpecifiers = $paramNames[2][$index] ?? '';

            $value = $matches[$index + 1];

            if ($this->isValueInvalid($value, $paramSpecifiers)) {
                throw new BadRequestHttpException("Некорректный параметр '$name'");
            }

            $params[$name] = $value;
        }

        return $params;
    }

    /**
     * @param string $value
     * @param string $paramSpecifiers
     * @return bool
     */
    private function isValueInvalid(string $value, string $paramSpecifiers): bool
    {
        $specifiers = explode('|', trim($paramSpecifiers, ':'));

        foreach ($specifiers as $specifier) {
            if ($specifier === 'integer' && is_numeric($value) === false) {
                return true;
            }

            if ($specifier === 'string' && is_string($value) === false) {
                return true;
            }

            if ($specifier === 'required' && empty($value) === true) {
                return true;
            }
        }

        return false;
    }

    // TODO prepareParams
    /**
     * @param array $middlewares
     *
     * @return void
     * @throws ReflectionException
     */
    private function processMiddlewares(array $middlewares): void
    {
        if (empty($middlewares) === true) {
            return;
        }

        foreach ($middlewares as $middleware) {
            $middlewareInstance = $this->container->make($middleware);

            if ($middlewareInstance instanceof CorsMiddlewareInterface) {
                $middlewareInstance->process($this->response);

                continue;
            }

            if ($middlewareInstance instanceof OptionsMiddlewareInterface) {
                $middlewareInstance->process($this->request, $this->response);

                continue;
            }

            $middlewareInstance->process($this->request);
        }
    }
}
