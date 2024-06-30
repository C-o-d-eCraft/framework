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
        private RequestInterface          $request,
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
            if ($this->handleRoute($route, $path, $method)) {
                $this->processMiddlewares($route->middlewares);

                [$controllerNameSpace, $action] = explode('::', $route->controllerAction);

                $controller = $this->container->make($controllerNameSpace);

                return $this->container->call($controller, $action);
            }
        }

        throw new NotFoundHttpException("Страница не найдена для $method запроса по пути $path");
    }

    private function handleRoute(Route $route, string $path, string $method): bool
    {
        $cleanPath = preg_replace('/\/\d+(?=\/|$)/', '', $path);
        $cleanRoute = preg_replace('/\/\{\w+\}(?=\/|$)/', '', $route->route);

        if (strcmp($cleanPath, $cleanRoute) === 0 && $route->method === $method) {
            preg_match_all('/\/(\d+)(?=\/|$)/', $path, $numberMatches);
            $numbers = $numberMatches[1];

            preg_match_all('/\{(\w+)\}/', $route->route, $keyMatches);
            $keys = $keyMatches[1];

            if (count($keys) === count($numbers)) {
                $params = array_combine($keys, $numbers);
                $this->request->getUri()->addPathVariables($params);
            }

            return true;
        }

        return false;
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
