<?php

namespace Tests\Unit;

use Craft\Components\DIContainer\DIContainer;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ResponseInterface;
use Craft\Contracts\RoutesCollectionInterface;
use Craft\Contracts\UriInterface;
use Craft\Http\Exceptions\NotFoundHttpException;
use Craft\Http\Route\Route;
use Craft\Http\Route\RouteParamsParser;
use Craft\Http\Route\Router;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use stdClass;

class RouterTest extends TestCase
{
    private function createRouter($container, $routesCollection, $request, $response, $routeParamsParser)
    {
        return new Router(
            $container,
            $routesCollection,
            $request,
            $response,
            $routeParamsParser
        );
    }

    public function testDispatchMethodCallsHandleRoute()
    {
        $request = $this->createMock(RequestInterface::class);
        $request->method('getMethod')->willReturn('GET');

        $routeParamsParser = $this->createMock(RouteParamsParser::class);
        $routeParamsParser->method('buildRegexFromRoute')->willReturn('#^/test$#');

        $uri = $this->createMock(UriInterface::class);
        $uri->method('getPath')->willReturn('/test');

        $request->method('getUri')->willReturn($uri);

        $route = $this->createMock(Route::class);
        $route->method = 'GET';
        $route->route = '/test';
        $route->handler = 'TestController::action';
        $route->middlewares = [];

        $routesCollection = $this->createMock(RoutesCollectionInterface::class);
        $routesCollection->method('getRoutes')->willReturn([$route]);

        $controller = $this->getMockBuilder(stdClass::class)
            ->addMethods(['action'])
            ->getMock();
        $controller->expects($this->once())->method('action')->willReturn($this->createMock(ResponseInterface::class));

        $container = $this->createMock(DIContainer::class);
        $container->method('make')->willReturn($controller);

        $response = $this->createMock(ResponseInterface::class);

        $router = $this->createRouter($container, $routesCollection, $request, $response, $routeParamsParser);

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

        $router = $this->createRouter(
            $this->createMock(DIContainer::class),
            $routesCollection,
            $request,
            $this->createMock(ResponseInterface::class),
            $this->createMock(RouteParamsParser::class)
        );

        $this->expectException(NotFoundHttpException::class);

        $router->dispatch();
    }
}
