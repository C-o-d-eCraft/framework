<?php

namespace Framework\Http;

use Framework\Contracts\HttpKernelInterface;
use Framework\Contracts\RequestInterface;
use Framework\Contracts\ResponseInterface;
use Framework\Contracts\RouterInterface;
use Framework\Http\Exceptions\HttpException;
use Framework\Http\Message\Stream;
use Framework\Http\ResponseTypes\HtmlResponse;
use Framework\Http\ResponseTypes\JsonResponse;
use Framework\Http\ResponseTypes\TextResponse;
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
        private readonly RouterInterface $router
    ) { }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
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
        } catch (HttpException $e) {
            $this->response->withStatus($e->getCode());
            $this->response->setReasonPhrase($e->getMessage());
            $this->response->setBody(new Stream($e->getMessage()));
        } catch (Throwable $e) {
            $this->response->withStatus(500);
            $this->response->setReasonPhrase('Внутренняя ошибка сервера');
            $this->response->setBody(new Stream($e->getMessage()));
        }

        return $this->response;
    }
}
