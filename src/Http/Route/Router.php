<?php

namespace Craft\Http\Route;

use Craft\Contracts\MiddlewareInterface;
use Craft\Components\DIContainer\DIContainer;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ResponseInterface;
use Craft\Contracts\RouterInterface;
use Craft\Contracts\RoutesCollectionInterface;
use Craft\Http\Exceptions\BadRequestHttpException;
use Craft\Http\Exceptions\NotFoundHttpException;
use ReflectionException;

readonly class Router implements RouterInterface
{
    /**
     * @param DIContainer $container
     * @param RoutesCollectionInterface $routesCollection
     * @param MiddlewareInterface $processMiddleware
     * @param RequestInterface $request
     */
    public function __construct(
        private DIContainer               $container,
        private RoutesCollectionInterface $routesCollection,
        private MiddlewareInterface       $processMiddleware,
        private RequestInterface          $request 
    ) { }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundHttpException
     * @throws ReflectionException
     * @throws BadRequestHttpException
     */
    public function dispatch(): ResponseInterface
    {
        $method = $this->request->getMethod();
        $path = $this->request->getUri()->getPath();

        $globalMiddleware = $this->routesCollection->getGlobalMiddlewares();

        foreach ($globalMiddleware as $middleware) {
            $middleware->process($this->request);
        }

        foreach ($this->routesCollection->getRoutes() as $route) {
            if ($route->route === $path && $route->method === $method) {

                $this->processMiddlewares($route->middlewares);

                $this->validateParams($route->params);

                [$controllerNameSpace, $action] = explode('::', $route->controllerAction);

                $controller = $this->container->make($controllerNameSpace);

                return $controller->{$action}($this->request);
            }
        }

        throw new NotFoundHttpException("Страница не найдена для $method запроса по пути $path");
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

    /**
     * @throws BadRequestHttpException
     */
    private function validateParams($params): void
    {
        if (empty($params[0]) === true || empty($this->request->getUri()->getQueryParams()) === true) {
            return;
        }

        foreach ($params as $param) {
            $paramName = $this->request->getUri()->getQueryParams()[$param['name']];

            if ($param['required'] && (empty($paramName)) === true) {
                throw new BadRequestHttpException("Обязательный параметр {$param['name']} отсутствует");
            }
            
            if ((isset($paramName )) === false) {
                $this->request->getUri()->addQueryParams([$param['name'] => $param['defaultValue']]);
            }

            if ($param['type'] === 'numeric' && (is_numeric($paramName )) === false) {
                throw new BadRequestHttpException("Параметр {$param['name']} должен быть числом");
            }
        }
    }
}
