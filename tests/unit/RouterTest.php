<?php

namespace Tests\Unit;

use Craft\Components\DIContainer\DIContainer;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\EventMessageInterface;
use Craft\Contracts\MiddlewareInterface;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ResponseInterface;
use Craft\Contracts\RoutesCollectionInterface;
use Craft\Contracts\UriInterface;
use Craft\Http\Exceptions\NotFoundHttpException;
use Craft\Http\Message\Request;
use Craft\Http\Route\Route;
use Craft\Http\Route\Router;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

class RouterTest extends TestCase
{
    private function createRouter($container, $routesCollection, $request)
    {
        return new Router(
            $container,
            $routesCollection,
            $this->createMock(MiddlewareInterface::class),
            $request,
            $this->createMock(EventMessageInterface::class),
            $this->createMock(EventDispatcherInterface::class)
        );
    }

    public function testDispatchMethodCallsHandleRoute()
    {
        $request = $this->createMock(Request::class);
        $request->method('getMethod')->willReturn('GET');

        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/test');

        $request->method('getUri')->willReturn($uri);

        $route = $this->createMock(Route::class);
        $route->route = '/test';
        $route->method = 'GET';
        $route->controllerAction = 'TestController::action';
        $route->middlewares = [];

        $routesCollection = $this->createMock(RoutesCollectionInterface::class);
        $routesCollection->method('getRoutes')->willReturn([$route]);

        $controller = $this->createMock(stdClass::class);

        $container = $this->createMock(DIContainer::class);
        $container->method('make')->willReturn($controller);

        $response = $this->createMock(ResponseInterface::class);
        $container->expects($this->once())->method('call')->willReturn($response);

        $router = $this->createRouter($container, $routesCollection, $request);

        $router->dispatch();
    }

    public function testDispatchMethodThrowsNotFoundException()
    {
        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn('GET');

        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/non-existent');

        $request->method('getUri')->willReturn($uri);

        $routesCollection = $this->createMock(RoutesCollectionInterface::class);
        $routesCollection->method('getRoutes')->willReturn([]);

        $router = $this->createRouter($this->createMock(DIContainer::class), $routesCollection, $request);

        $this->expectException(NotFoundHttpException::class);

        $router->dispatch();
    }
}
