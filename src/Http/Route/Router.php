<?php

namespace Craft\Http\Route;

use Craft\Components\DIContainer\DIContainer;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ResponseInterface;
use Craft\Contracts\RouterInterface;
use Craft\Contracts\RoutesCollectionInterface;
use Craft\Http\Exceptions\BadRequestHttpException;
use Craft\Http\Exceptions\NotFoundHttpException;
use Craft\Http\Validator\Validator;
use Craft\Http\Route\DTO\Route;

class Router implements RouterInterface
{
    public function __construct(
        private DIContainer               $container,
        private RoutesCollectionInterface $routesCollection,
        private RequestInterface          $request,
        private ResponseInterface         $response,
        private RouteParamsParser         $paramsParser,
    ) {
    }

    /**
     * @return ResponseInterface
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     */
    public function dispatch(): ResponseInterface
    {
        $method = $this->request->getMethod();
        $path = $this->request->getUri()->getPath();

        foreach ($this->routesCollection->getRoutes() as $route) {
            $params = [];

            if ($this->matchRoute($route, $path, $method, $params)) {
                [$controllerClass, $action] = explode('::', $route->handler);

                $handler = function (RequestInterface $request, ResponseInterface $response) use ($controllerClass, $action, $params) {
                    $controller = $this->container->make($controllerClass);

                    return $controller->$action(...array_values($params));
                };

                return $this->processMiddlewares($route->middlewares, $handler, $params);
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
     * @throws BadRequestHttpException
     */
    private function matchRoute(Route $route, string $path, string $method, array &$params): bool
    {
        if ($route->method !== $method) {
            return false;
        }

        $routePattern = $this->paramsParser->buildRegexFromRoute($route->path);

        if ((bool)preg_match($routePattern, $path, $matches) === false) {
            return false;
        }

        $params = $this->extractAndValidateParams($route->path, $matches);

        if ($this->paramsParser->hasQueryParameters($route->path)) {
            $queryParams = $this->request->getUri()->getQueryParams();
            $params += $this->extractAndValidateParams($route->path, $queryParams, true);
        }

        return true;
    }

    /**
     * @param string $route
     * @param array $paramsData
     * @param bool $isQuery
     * @return array
     * @throws BadRequestHttpException
     */
    private function extractAndValidateParams(string $route, array $paramsData, bool $isQuery = false): array
    {
        $parameters = $isQuery
            ? $this->paramsParser->parseQueryParameters($route, $paramsData)
            : $this->paramsParser->parseRouteParameters($route, $paramsData);

        $this->validateParameters($parameters);

        return array_map(fn($paramDTO) => $paramDTO->value, $parameters);
    }

    /**
     * @param array $parameters
     * @return void
     * @throws BadRequestHttpException
     */
    private function validateParameters(array $parameters): void
    {
        $validator = new Validator(array_column($parameters, 'value', 'name'));

        foreach ($parameters as $parameter) {
            foreach ($parameter->specifiers as $specifier) {
                $validator->apply([$parameter->name, $specifier]);
            }
        }

        if ($errors = $validator->getErrors()) {
            throw new BadRequestHttpException(
                'Некорректные параметры маршрута: ' . json_encode($errors, JSON_UNESCAPED_UNICODE)
            );
        }
    }

    /**
     * @param array $middlewares
     * @param callable $handler
     * @param array|null $params
     * @return ResponseInterface
     */
    private function processMiddlewares(array $middlewares, callable $handler, ?array $params = null): ResponseInterface
    {
        $chain = array_reduce(
            array_reverse($middlewares),
            fn($next, $middleware) => function (RequestInterface $request, ResponseInterface $response) use ($middleware, $next, $params) {
                $middlewareInstance = $this->container->make($middleware);

                return $middlewareInstance->process($request, $response, $next, $params);
            },
            $handler
        );

        return $chain($this->request, $this->response);
    }
}
