<?php

namespace Craft\Http;

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
use Craft\Components\Logger\Logger;
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
    ) { }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        try {
            $this->response = $this->router->dispatch($this->request);

            $contentType = match (true) {
                $this->response instanceof JsonResponse => 'application/json',
                $this->response instanceof TextResponse => 'text/plain',
                $this->response instanceof HtmlResponse => 'text/html',
                default => null,
            };

            if ($contentType !== null) {
                $this->response->withHeader('Content-Type', $contentType);
            }

            $method = $this->request->getMethod();
            if (in_array($method, ['GET', 'PUT', 'PATCH'])) {
                $this->response->withStatus(200);
            } 
            
            if ($method === 'POST') {
                $this->response->withStatus(201);
            } 
            
            if ($method === 'DELETE') {
                $this->response->withStatus(204);
            }
        } catch (\AssertionError $e) {
            $this->handleException($e, 401, 'Ошибка авторизации');
        } catch (\InvalidArgumentException $e) {
            $this->handleException($e, 400, 'Ошибка ввода');
        } catch (\LogicException $e) {
            $this->handleException($e, 404, 'Логическая ошибка');
        } catch (Throwable $e) {
            $this->handleException($e, 500, 'Ошибка сервера при обработке запроса');
        }

        return $this->response;
    }

    private function handleException(Throwable $exception, int $statusCode, string $errorMessage): void
    {
        $this->response->withStatus($statusCode);
        $xDebugTag = $this->logger->getXDebugTag();

        $errorData = [
            'message' => $errorMessage,
            'x-debug-tag' => $xDebugTag,
        ];

        $this->logger->writeLog($exception, $errorMessage);

        $this->response->setBody(new Stream($exception->getMessage()));
    }
}
