<?php

namespace Craft\Http;

use Craft\Components\DIContainer\DIContainer;
use Craft\Components\ErrorHandler\HttpErrorHandler;
use Craft\Components\ErrorHandler\MessageEnum;
use Craft\Components\ErrorHandler\StatusCodeEnum;
use Craft\Contracts\ErrorHandlerInterface;
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
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param RouterInterface $router
     */
    public function __construct(
        private readonly RequestInterface $request,
        private ResponseInterface         $response,
        private readonly RouterInterface  $router,
        private readonly LoggerInterface  $logger,
        private ErrorHandlerInterface     $errorHandler,
        private DIContainer               $container,
    ) { }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        try {
            $this->logger->info('Запуск контроллера');

            $this->response = $this->router->dispatch($this->request);

            if ($this->response instanceof JsonResponse) {
                $this->response->withHeader('Content-Type','application/json');
            }

            if ($this->response instanceof TextResponse) {
                $this->response->withHeader('Content-Type','text/plain');
            }

            if ($this->response instanceof HtmlResponse) {
                $this->response->withHeader('Content-Type','text/html');
            }
        } catch (HttpException $e) {
            $this->response->withStatus($e->getCode());
            $this->response->setReasonPhrase($e->getMessage());

            $this->logger->error($e->getMessage(), ['exception' => $e]);

            $errorsView = $this->container->call(HttpErrorHandler::class, 'handle', [$e]);

            $this->response->setBody(new Stream($errorsView));
        } catch (Throwable $e) {
            $this->response->withStatus(StatusCodeEnum::INTERNAL_SERVER_ERROR);
            $this->response->setReasonPhrase(MessageEnum::INTERNAL_SERVER_ERROR);

            $this->logger->critical(MessageEnum::INTERNAL_SERVER_ERROR, ['exception' => $e]);

            $errorsView = $this->container->call(HttpErrorHandler::class, 'handle', [$e]);

            $this->response->setBody(new Stream($errorsView));
        }

        return $this->response;
    }
}
