<?php

namespace Craft\Components\ErrorHandler;

use Craft\Contracts\ErrorHandlerInterface;
use Craft\Contracts\LoggerInterface;
use Craft\Contracts\RequestInterface;
use Craft\Contracts\ViewInterface;
use Throwable;

class HttpErrorHandler implements ErrorHandlerInterface 
{
    /**
     * @param ViewInterface $view
     * @param LoggerInterface $logger
     *
     */
    public function __construct(
        private ViewInterface $view,
        private LoggerInterface $logger,
        private readonly RequestInterface $request,
    ) { }

    /**
     * @param Throwable $exception
     * @param string|null $statusCode
     * @param string|null $reasonPhrase
     * @return string
     */
    public function handle(Throwable $exception, string $statusCode = null, string $reasonPhrase = null): string
    {
        if (file_exists($this->view->getBasePath() . 'ErrorView.php') === false) {
            $this->view->setBasePath(__DIR__ . '/../../Http/View/ErrorsTemplate');
        }

        $requestContentType = $this->request->getHeaders()['CONTENT-TYPE'] ?? null;

        if ($requestContentType === 'application/json' || getenv('RENDER_MODE') === 'SPA') {
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
        $params = [
            'statusCode' => $statusCode ?? $exception->getCode(),
            'reasonPhrase' => $reasonPhrase ?? $exception->getMessage(),
        ];

        if (getenv('ENV') === 'development') {
            $params = array_merge($params, [
                'xdebugTag' => defined('X_DEBUG_TAG') ? X_DEBUG_TAG : null,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'stackTrace' => explode(PHP_EOL, $exception->getTraceAsString()),
            ]);
        }

        return $this->view->render('ErrorView', $params);
    }

    public function getJsonErrorBody(Throwable $exception, string $statusCode = null, string $reasonPhrase = null): string
    {
        $params = [
            'statusCode' => $statusCode ?? $exception->getCode(),
            'reasonPhrase' => $reasonPhrase ?? $exception->getMessage(),
        ];

        if (getenv('ENV') === 'development') {
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