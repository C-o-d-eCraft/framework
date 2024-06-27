<?php

namespace Craft\Components\ErrorHandler;

use Craft\Contracts\ErrorHandlerInterface;
use Craft\Contracts\LoggerInterface;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ViewInterface;
use Throwable;

class HttpErrorHandler implements ErrorHandlerInterface 
{
    public function __construct(
        private ViewInterface $view,
        private LoggerInterface $logger,
        private readonly RequestInterface $request,
        private ?string $environmentMode = null,
        private ?string $customErrorViewPath = null,
        private ?string $customErrorViewName = null
    ) { }

    /**
     * @param Throwable $exception
     * @param string|null $statusCode
     * @param string|null $reasonPhrase
     * @return string
     */
    public function handle(Throwable $exception, string $statusCode = null, string $reasonPhrase = null): string
    {
        $this->view->setBasePath(__DIR__ . '/../../Http/View/ErrorsTemplate');

        if (is_null($this->customErrorViewPath) === false) {
            $this->view->setBasePath($this->customErrorViewPath);
        }

        $requestContentType = $this->request->getHeaders()['CONTENT-TYPE'] ?? null;

        if ($requestContentType === 'application/json') {
            return $this->getJsonErrorBody($exception, $statusCode, $reasonPhrase);
        }

        return $this->getHttpErrorView($exception, $statusCode, $reasonPhrase);
    }

    /**
     * @param Throwable $exception
     * @return string
     */
    public function getHttpErrorView(Throwable $exception, string $statusCode = null, string $reasonPhrase = null): string
    {
        $baseViewName = 'ErrorView';

        if (is_null($this->customErrorViewName) === false) {
            $baseViewName = $this->customErrorViewName;
        }

        $params = [
            'statusCode' => $statusCode ?? $exception->getCode(),
            'reasonPhrase' => $reasonPhrase ?? $exception->getMessage(),
        ];

        if ($this->environmentMode === 'development') {
            $params = array_merge($params, [
                'xdebugTag' => defined('X_DEBUG_TAG') ? X_DEBUG_TAG : null,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'stackTrace' => explode(PHP_EOL, $exception->getTraceAsString()),
            ]);
        }

        return $this->view->render($baseViewName, $params);
    }

    public function getJsonErrorBody(Throwable $exception, string $statusCode = null, string $reasonPhrase = null): string
    {
        $params = [
            'statusCode' => $statusCode ?? $exception->getCode(),
            'reasonPhrase' => $reasonPhrase ?? $exception->getMessage(),
        ];

        if ($this->environmentMode === 'development') {
            $params = array_merge($params, [
                'xdebugTag' => defined('X_DEBUG_TAG') ? X_DEBUG_TAG : null,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'stackTrace' => explode(PHP_EOL, $exception->getTraceAsString()),
            ]);
        }

        return json_encode($params);
    }
}
