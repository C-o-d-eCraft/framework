<?php

namespace Craft\Http;

use Craft\Contracts\HttpKernelInterface;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ResponseInterface;
use Craft\Contracts\RouterInterface;
use Craft\Http\Message\Stream;
use Craft\Http\ResponseTypes\HtmlResponse;
use Craft\Http\ResponseTypes\JsonResponse;
use Craft\Http\ResponseTypes\TextResponse;
use Craft\Components\Logger\Logger;

class HttpKernel implements HttpKernelInterface
{
    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param RouterInterface $router
     * @param Logger $logger
     */
    public function __construct(
        private readonly RequestInterface $request,
        private ResponseInterface         $response,
        private readonly RouterInterface  $router,
        private readonly Logger           $logger,
    ) { }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function handle(RequestInterface $request): ResponseInterface
    {
        try {
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
        } catch (\AssertionError $e) {
            $this->response->withStatus(401);

            $xDebugTag = $this->logger->getXDebugTag();

            $this->response->setBody(new Stream(json_encode([
                'message' => 'Авторизация не выполнена',
                'x-debug-tag' => $xDebugTag,
            ])));

            $this->logger->writeLog($e, 'Ошибка авторизации', $this->logger->handleContext, getallheaders(), $xDebugTag);
        } catch (\InvalidArgumentException $e) {
            $this->response->withStatus(400);

            $xDebugTag = $this->logger->getXDebugTag();

            $this->response->setBody(new Stream(json_encode([
                'message' => $e->getMessage(),
                'x-debug-tag' => $xDebugTag,
            ])));

            $this->logger->writeLog($e,'Ошибка ввода', $this->logger->handleContext, $xDebugTag);
        }
        catch (\LogicException $e) {
            $this->response->withStatus(404);

            $xDebugTag = $this->logger->getXDebugTag();

            $this->response->setBody(new Stream(json_encode([
                'message' => $e->getMessage(),
                'x-debug-tag' => $xDebugTag,
            ])));

            $this->logger->writeLog($e, 'Логическая ошибка', $this->logger->handleContext, $xDebugTag);
        } catch (\Throwable $e) {
            $this->response->withStatus(500);

            $xDebugTag = $this->logger->getXDebugTag();

            $this->response->setBody(new Stream(json_encode([
                'message' => 'Ошибка сервера при обработке запроса',
                'x-debug-tag' => $xDebugTag,
            ])));

            $this->logger->writeLog($e, 'Ошибка сервера', $this->logger->handleContext, $xDebugTag);
        }

        return $this->response;
    }
}
