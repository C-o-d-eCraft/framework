<?php

namespace Craft\Http\Route;

use Craft\Components\DIContainer\DIContainer;
use Craft\Components\EventDispatcher\EventMessage;
use Craft\Components\Logger\StateProcessor\LogContextEvent;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\EventMessageInterface;
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
        private RequestInterface          $request,
        private EventMessageInterface     $eventMessage,
        private EventDispatcherInterface  $eventDispatcher
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
            if ($this->matchRoute($route, $path, $method)) {
                $this->processMiddlewares($route->middlewares);

                [$controllerNameSpace, $action] = explode('::', $route->controllerAction);

                $controller = $this->container->make($controllerNameSpace);

                return $this->container->call($controller, $action);
            }
        }

        throw new NotFoundHttpException("Страница не найдена для $method запроса по пути $path");
    }

    /**
     * Проверка, соответствует ли данный маршрут пути и метод запроса.
     *
     * @param Route $route
     * @param string $path
     * @param string $method
     * @return bool
     */
    private function matchRoute(Route $route, string $path, string $method): bool
    {
        $pattern = preg_quote($route->route, '/');

        $pattern = preg_replace('/\{(\w+)\}/', '(?<$1>\w+)', $pattern);

        $pattern = "/^$pattern$/";

        return preg_match($pattern, $path) && $route->method === $method;
    }



    /**
     * @param array $middlewares
     *
     * @return void
     * @throws ReflectionException
     */
    private function processMiddlewares(array $middlewares): void
    {
        if (empty($middlewares)) {
            return;
        }

        foreach ($middlewares as $middleware) {
            $middlewareInstance = $this->container->make($middleware);

            $middlewareInstance->process($this->request);
        }
    }
}
