<?php

namespace Craft\Components\ErrorHandler;

use Craft\Contracts\DebugTagStorageInterface;
use Craft\Contracts\ErrorHandlerInterface;
use Craft\Contracts\LoggerInterface;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ViewInterface;
use Throwable;

class HttpErrorHandler implements ErrorHandlerInterface
{
    public function __construct(
        private ViewInterface             $view,
        private LoggerInterface           $logger,
        private DebugTagStorageInterface  $debugTagStorage,
        private readonly RequestInterface $request,
        private ?string                   $environmentMode = null,
        private ?string                   $customErrorViewPath = null,
        private ?string                   $customErrorViewName = null
    )
    {
    }

    /**
     * @param Throwable $exception
     * @param string|null $statusCode
     * @param string|null $reasonPhrase
     *
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

    public function getHttpErrorView(Throwable $exception, string $statusCode = null, string $reasonPhrase = null): string
    {
        $baseViewName = 'ErrorView';

        if (is_null($this->customErrorViewName) === false) {
            $baseViewName = $this->customErrorViewName;
        }

        $params = [
            'xdebugTag' => $this->debugTagStorage->getTag(),
            'statusCode' => $statusCode ?? $exception->getCode(),
            'reasonPhrase' => $reasonPhrase ?? $exception->getMessage(),
            'environmentMode' => $this->environmentMode,
        ];

        $result = $this->checkEnvironmentMode($params, $exception);

        return $this->view->render($baseViewName, $result);
    }

    public function getJsonErrorBody(Throwable $exception, string $statusCode = null, string $reasonPhrase = null): string
    {
        $params = [
            'xdebugTag' => $this->debugTagStorage->getTag(),
            'statusCode' => $statusCode ?? $exception->getCode(),
            'reasonPhrase' => $reasonPhrase ?? $exception->getMessage(),
        ];

        $result = $this->checkEnvironmentMode($params, $exception);

        return json_encode($result);
    }

    private function checkEnvironmentMode(array $params, Throwable $exception): array
    {
        if ($this->environmentMode === 'development') {
            $params = array_merge($params, [
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'stackTrace' => explode(PHP_EOL, $exception->getTraceAsString()),
            ]);
        }

        return $params;
    }
}
