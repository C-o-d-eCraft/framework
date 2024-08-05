<?php

namespace Tests\Unit;

use Craft\Contracts\UriInterface;
use Craft\Http\Exceptions\HttpException;
use Craft\Http\Exceptions\NotFoundHttpException;
use Craft\Http\Route\Route;
use Craft\Http\Route\Router;
use Craft\Components\DIContainer\DIContainer;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\RoutesCollectionInterface;
use Craft\Contracts\ResponseInterface;
use Craft\Contracts\MiddlewareInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionException;
use stdClass;


class RouterTest extends TestCase
{
    private DIContainer $container;
    private RoutesCollectionInterface $routesCollection;
    private MiddlewareInterface $middleware;
    private RequestInterface $request;
    private Router $router;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->container = $this->createMock(DIContainer::class);
        $this->routesCollection = $this->createMock(RoutesCollectionInterface::class);
        $this->middleware = $this->createMock(MiddlewareInterface::class);
        $this->request = $this->createMock(RequestInterface::class);

        $this->router = new Router(
            $this->container,
            $this->routesCollection,
            $this->middleware,
            $this->request
        );
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws ReflectionException
     * @throws HttpException
     */
    public function testDispatchMethodCallsHandleRoute()
    {
        $this->request->method('getMethod')->willReturn('GET');
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/test');
        $this->request->method('getUri')->willReturn($uri);

        $route = $this->createMock(Route::class);
        $route->route = '/test';
        $route->method = 'GET';
        $route->controllerAction = 'TestController::action';
        $route->middlewares = [];

        $this->routesCollection->method('getRoutes')->willReturn([$route]);

        $controller = $this->createMock(stdClass::class);
        $this->container->method('make')->willReturn($controller);
        $response = $this->createMock(ResponseInterface::class);
        $this->container->method('call')->willReturn($response);

        $result = $this->router->dispatch();

        $this->assertSame($response, $result);
    }

    /**
     * @throws Exception
     * @throws HttpException
     * @throws NotFoundHttpException
     * @throws ReflectionException
     */
    public function testDispatchThrowsNotFoundHttpException()
    {
        $this->request->method('getMethod')->willReturn('GET');
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/invalid-path');
        $this->request->method('getUri')->willReturn($uri);

        $this->routesCollection->method('getRoutes')->willReturn([]);

        $this->expectException(NotFoundHttpException::class);

        $this->router->dispatch();
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     * @throws ReflectionException
     * @throws HttpException
     */
    public function testProcessMiddlewaresIsCalled()
    {
        $this->request->method('getMethod')->willReturn('GET');
        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/test');
        $this->request->method('getUri')->willReturn($uri);

        $route = $this->createMock(Route::class);
        $route->route = '/test';
        $route->method = 'GET';
        $route->controllerAction = 'TestController::action';
        $route->middlewares = [get_class($this->middleware)];

        $this->routesCollection->method('getRoutes')->willReturn([$route]);

        $this->container->method('make')->willReturn($this->middleware);
        $this->middleware->expects($this->once())->method('process')->with($this->request);

        $this->container->method('call')->willReturn($this->createMock(ResponseInterface::class));

        $this->router->dispatch();
    }
}
