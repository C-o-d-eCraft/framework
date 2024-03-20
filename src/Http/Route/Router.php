<?php

namespace Framework\Http\Route;

use app\Http\Middlewares\MiddlewareInterface;
use Framework\Components\DIContainer\DIContainer;
use Framework\Contracts\RequestInterface;
use Framework\Contracts\ResponseInterface;
use Framework\Contracts\RouterInterface;
use Framework\Contracts\RoutesCollectionInterface;
use Framework\Http\Exceptions\BadRequestHttpException;
use Framework\Http\Exceptions\NotFoundHttpException;
use ReflectionException;

readonly class Router implements RouterInterface
{
    /**
     * @param DIContainer $container
     * @param RoutesCollectionInterface $routesCollection
     * @param MiddlewareInterface $loggingMiddleware
     */
    public function __construct(
        private DIContainer               $container,
        private RoutesCollectionInterface $routesCollection,
        private MiddlewareInterface       $loggingMiddleware,
    ) { }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws NotFoundHttpException
     * @throws ReflectionException
     * @throws BadRequestHttpException
     */
    public function dispatch(RequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();
        $path = $request->getUri()->getPath();
        $this->routesCollection->addGlobalMiddleware($this->loggingMiddleware);

        $globalMiddleware = $this->routesCollection->getGlobalMiddlewares();

        foreach ($globalMiddleware as $middleware) {
            $middleware->process($request);
        }

        foreach ($this->routesCollection->getRoutes() as $route) {
            $this->validateParams($route->params, $request);
        }

        foreach ($this->routesCollection->getRoutes() as $route) {
            if ($route->route === $path && $route->method === $method) {
                [$controllerNameSpace, $action] = explode('::', $route->controllerAction);

                $controller = $this->container->make($controllerNameSpace);

                return $controller->{$action}($request);
            }
        }

        throw new NotFoundHttpException("Страница не найдена для $method запроса по пути $path");
    }

    /**
     * @throws BadRequestHttpException
     */
    private function validateParams($params, RequestInterface $request): void
    {
        foreach ($params as $param) {
            if ($param['required'] && (isset($request->getUri()->getQueryParams()[$param['name']])) === false) {
                throw new BadRequestHttpException("Обязательный параметр {$param['name']} отсутствует");
            }

            if ((isset($request->getUri()->getQueryParams()[$param['name']])) === false) {
                $request->getUri()->addQueryParams([$param['name'] => $param['defaultValue']]);
            }

            if ($param['type'] === 'numeric' && (is_numeric($request->getUri()->getQueryParams()[$param['name']])) === false) {
                throw new BadRequestHttpException("Параметр {$param['name']} должен быть числом");
            }
        }
    }
}
