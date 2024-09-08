<?php
declare(strict_types = 1);

namespace Craft\Http\Route;

use Craft\Components\DIContainer\DIContainer;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ResponseInterface;
use Craft\Contracts\RouterInterface;
use Craft\Contracts\RoutesCollectionInterface;
use Craft\Http\Exceptions\BadRequestHttpException;
use Craft\Http\Exceptions\HttpException;
use Craft\Http\Exceptions\NotFoundHttpException;
use Craft\Http\Message\Stream;
use Craft\Http\ResponseTypes\JsonResponse;
use ReflectionException;

class Router implements RouterInterface
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
        private ResponseInterface         $response
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
        $queryParams = $this->request->getUri()->getQueryParams();

        foreach ($this->routesCollection->getRoutes() as $route) {
            $params = [];

            if ($this->handleRoute($route, $path, $method, $params, $queryParams)) {
                [$controllerClass, $action] = explode('::', $route->handler);

                $handler = function (RequestInterface $request, ResponseInterface $response) use ($controllerClass, $action, $params) {
                    $controller = $this->container->make($controllerClass);

                    return $controller->$action(...array_values($params));
                };

                return $this->processMiddlewares($route->middlewares, $handler);
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
    private function handleRoute(Route $route, string $path, string $method, array &$params = [], array $queryParams = []): bool
    {
        $routePattern = $this->buildRoutePattern($route->route);

        if (preg_match($routePattern, $path, $matches) && $route->method === $method) {
            $params = $this->extractParams($route->route, $matches);

            if ($this->hasQueryParams($route->route) === true) {
                $queryParamRules = $this->extractQueryParamRules($route->route);

                $this->validateQueryParams($queryParams, $queryParamRules);

                $params = array_merge($params, $queryParams);
            }

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
        $routeWithoutQuery = explode('?', $route)[0];
        $pattern = preg_replace_callback('/\{(\w+)(:[^}]+)?\}/', function ($matches) {
            $paramPattern = $matches[2] ?? '';

            return '(' . ($this->convertParamPattern($paramPattern) ?: '\w+') . ')';
        }, $routeWithoutQuery);

        return '#^' . $pattern . '$#';
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

            if ($this->isValueInvalid($value, $paramSpecifiers) === true) {
                throw new BadRequestHttpException('Не корректные параметры поиска');
            }

            $params[$name] = $value;
        }

        return $params;
    }

    /**
     * @param string $route
     * @return bool
     */
    private function hasQueryParams(string $route): bool
    {
        return strpos($route, '?') !== false;
    }

    /**
     * @param string $route
     * @return array
     */
    private function extractQueryParamRules(string $route): array
    {
        $parts = explode('?', $route);
        $queryParams = $parts[1] ?? '';
        $rules = [];

        foreach (explode('&', $queryParams) as $param) {
            if (strpos($param, ':') !== false) {
                [$name, $specifiers] = explode(':', $param);
                $rules[$name] = $specifiers;
            }
        }

        return $rules;
    }

    /**
     * @param array $queryParams
     * @param array $rules
     * @return void
     * @throws BadRequestHttpException
     */
    private function validateQueryParams(array $queryParams, array $rules): ResponseInterface
    {
        foreach ($rules as $name => $specifiers) {
            $value = $queryParams[$name] ?? null;

            if ($this->isValueInvalid($value, $specifiers) === true) {
                throw new BadRequestHttpException('Не корректные параметры поиска');
            }
        }

        return $this->response;
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

            if (str_starts_with($specifier, 'minLength=') && strlen($value) < (int)str_replace('minLength=', '', $specifier)) {
                return true;
            }

            if (str_starts_with($specifier, 'maxLength=') && strlen($value) > (int)str_replace('maxLength=', '', $specifier)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $middlewares
     *
     * @return void
     * @throws ReflectionException
     */
    private function processMiddlewares(array $middlewares, callable $handler): ResponseInterface
    {
        $chain = array_reduce(
            array_reverse($middlewares),
            function ($next, $middleware) {
                return function (RequestInterface $request, ResponseInterface $response) use ($middleware, $next) {
                    $middlewareInstance = $this->container->make($middleware);

                    return $middlewareInstance->process($request, $response, $next);
                };
            },
            $handler
        );

        return $chain($this->request, $this->response);
    }
}
