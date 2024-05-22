<?php

namespace Craft\Http\Route;

use Craft\Components\DIContainer\DIContainer;
use Craft\Contracts\MiddlewareInterface;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ResponseInterface;
use Craft\Contracts\RouterInterface;
use Craft\Contracts\RoutesCollectionInterface;
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
        private MiddlewareInterface       $middleware,
        private RequestInterface          $request
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
            if ($route->route === $path && $route->method === $method) {
                $this->processMiddlewares($route->middlewares);

                $this->validateParams($route->params);

                [$controllerNameSpace, $action] = explode('::', $route->controllerAction);

                $controller = $this->container->make($controllerNameSpace);

                return $this->container->call($controllerNameSpace, $action);
            }
        }

        throw new NotFoundHttpException("Страница не найдена для $method запроса по пути $path");
    }

    /**
     * @param $params
     * @return void
     * @throws HttpException
     */
    private function validateParams($params): void
    {
        if (empty($params[0]) && empty($this->request->getUri()->getQueryParams())) {
            return;
        }

        foreach ($params as $param) {
            $paramName = $this->request->getUri()->getQueryParams()[$param['name']];

            if ($param['required'] && (empty($paramName))) {
                throw new HttpException("Обязательный параметр {$param['name']} отсутствует");
            }

            if ((isset($paramName )) === false) {
                $this->request->getUri()->addQueryParams([$param['name'] => $param['defaultValue']]);
            }

            if ($param['type'] === 'numeric' && (is_numeric($paramName )) === false) {
                throw new HttpException("Параметр {$param['name']} должен быть числом");
            }
        }
    }

    /**
     * @param array $middlewares
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

            $middlewareInstance->process($this->request);
        }
    }
}
