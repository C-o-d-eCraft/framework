<?php

namespace Craft\Http;

use Craft\Components\DIContainer\DIContainer;
use Craft\Components\ErrorHandler\ErrorMessage;
use Craft\Components\ErrorHandler\StatusCode;
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
        private ResponseInterface         $response,
        private readonly RouterInterface  $router,
        private LoggerInterface           $logger,
        private ErrorHandlerInterface     $errorHandler,
        private EventDispatcherInterface  $eventDispatcher,
        private DIContainer               $container,
    ) {
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        try {
            $this->response = $this->router->dispatch($request);

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
            $this->response->withStatus(StatusCode::INTERNAL_SERVER_ERROR->value);
            $this->response->setReasonPhrase(ErrorMessage::INTERNAL_SERVER_ERROR->value);

            $this->logger->critical($e);

            $errorsView = $this->container->call($this->errorHandler, 'handle', [$e]);

            $this->response->setBody(new Stream($errorsView));
        } finally {
            if (isset($errorsView) === true) {
                $this->response->setStatusCode((json_decode($errorsView, true)['statusCode']) ?? StatusCode::INTERNAL_SERVER_ERROR->value);
            }

            if (isset($request->getHeaders()['CONTENT-TYPE']) && $request->getHeaders()['CONTENT-TYPE'] === 'application/json') {
                $this->response->withHeader('Content-Type', 'application/json');
            }
        }

        return $this->response;
    }
}
