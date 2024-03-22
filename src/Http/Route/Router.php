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
     * @param MiddlewareInterface $globalMiddleware
     * @param RequestInterface $request
     */
    public function __construct(
        private DIContainer               $container,
        private RoutesCollectionInterface $routesCollection,
        private MiddlewareInterface       $globalMiddleware,
        private RequestInterface          $request
    ) { }

    /**
     * @return ResponseInterface
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws ReflectionException
     */
    public function dispatch(): ResponseInterface
    {
        $method = $this->request->getMethod();
        $path = $this->request->getUri()->getPath();
        $this->routesCollection->addGlobalMiddleware($this->globalMiddleware);

        $globalMiddleware = $this->routesCollection->getGlobalMiddlewares();

        foreach ($globalMiddleware as $middleware) {
            $middleware->process($this->request);
        }

        foreach ($this->routesCollection->getRoutes() as $route) {
            $this->validateParams($route->params);
        }

        foreach ($this->routesCollection->getRoutes() as $route) {
            if ($route->route === $path && $route->method === $method) {
                [$controllerNameSpace, $action] = explode('::', $route->controllerAction);

                $controller = $this->container->make($controllerNameSpace);

                return $controller->{$action}($this->request);
            }
        }

        throw new NotFoundHttpException("Страница не найдена для $method запроса по пути $path");
    }

    /**
     * @throws BadRequestHttpException
     */
    private function validateParams($params): void
    {
        if (empty($params[0]) === true && empty($this->request->getUri()->getQueryParams()) === true) {
            return;
        }

        foreach ($params as $param) {
            $paramName = $this->request->getUri()->getQueryParams()[$param['name']];

            if ($param['required'] && (isset($paramName)) === false) {
                throw new BadRequestHttpException("Обязательный параметр {$param['name']} отсутствует");
            }

            if ((isset($paramName)) === false) {
                $this->request->getUri()->addQueryParams([$param['name'] => $param['defaultValue']]);
            }

            if ($param['type'] === 'numeric' && (is_numeric($paramName)) === false) {
                throw new BadRequestHttpException("Параметр {$param['name']} должен быть числом");
            }
        }
    }
}
