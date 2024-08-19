<?php

namespace Craft\Http;

use Craft\Components\DIContainer\DIContainer;
use Craft\Components\ErrorHandler\MessageEnum;
use Craft\Components\ErrorHandler\StatusCodeEnum;
use Craft\Contracts\ErrorHandlerInterface;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\HttpKernelInterface;
use Craft\Contracts\LoggerInterface;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ResponseInterface;
use Craft\Contracts\RouterInterface;
use Craft\Http\Exceptions\HttpException;
use Craft\Http\Message\Stream;
use Craft\Http\ResponseTypes\HtmlResponse;
use Craft\Http\ResponseTypes\JsonResponse;
use Craft\Http\ResponseTypes\TextResponse;
use Throwable;

class HttpKernel implements HttpKernelInterface
{
    public function __construct(
        private readonly RequestInterface $request,
        private ResponseInterface         $response,
        private readonly RouterInterface  $router,
        private LoggerInterface           $logger,
        private ErrorHandlerInterface     $errorHandler,
        private EventDispatcherInterface  $eventDispatcher,
        private DIContainer               $container,
    ) { }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        if ($this->request->getMethod() === 'OPTIONS') {
            $this->response->withStatus(200);
            $this->addCorsHeaders();

            return $this->response;
        }

        try {
            $this->response = $this->router->dispatch($this->request);

            if ($this->response instanceof JsonResponse) {
                $this->response->withHeader('Content-Type', 'application/json');
            }

            if ($this->response instanceof TextResponse) {
                $this->response->withHeader('Content-Type', 'text/plain');
            }

            if ($this->response instanceof HtmlResponse) {
                $this->response->withHeader('Content-Type', 'text/html');
            }
        } catch (HttpException $e) {
            $this->response->withStatus($e->getCode());
            $this->response->setReasonPhrase($e->getMessage());

            $this->logger->error($e);

            $errorsView = $this->container->call($this->errorHandler, 'handle', [$e]);

            $this->response->setBody(new Stream($errorsView));
        } catch (Throwable $e) {
            $this->response->withStatus(StatusCodeEnum::INTERNAL_SERVER_ERROR);
            $this->response->setReasonPhrase(MessageEnum::INTERNAL_SERVER_ERROR);

            $this->logger->critical($e);

            $errorsView = $this->container->call($this->errorHandler, 'handle', [$e]);

            $this->response->setBody(new Stream($errorsView));
        } finally {
            if (isset($errorsView) === true) {
                $this->response->setStatusCode((json_decode($errorsView, true)['statusCode']) ?? StatusCodeEnum::INTERNAL_SERVER_ERROR);
            }

            if (isset($this->request->getHeaders()['CONTENT-TYPE']) && $this->request->getHeaders()['CONTENT-TYPE'] === 'application/json') {
                $this->response->withHeader('Content-Type', 'application/json');
            }
        }

        $this->addCorsHeaders();

        return $this->response;
    }

    private function addCorsHeaders(): void
    {
        $this->response->withHeader('Access-Control-Allow-Origin', '*');
        $this->response->withHeader('Access-Control-Allow-Methods', '*');
        $this->response->withHeader('Access-Control-Allow-Headers', '*');
        $this->response->withHeader('Access-Control-Allow-Credentials', 'true');
    }
}
