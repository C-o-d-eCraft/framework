<?php

namespace Craft\Components\ErrorHandler;

use Craft\Contracts\LoggerInterface;
use Craft\Contracts\ViewInterface;
use Craft\Contracts\ErrorHandlerInterface;
use Craft\Http\Exceptions\BadRequestHttpException;
use Craft\Http\Exceptions\ForbiddenHttpException;
use Craft\Http\Exceptions\NotFoundHttpException;
use Craft\Http\View\View;
use Craft\Http\Exceptions\HttpException;
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
    ) {   }

    /**
     * @param Throwable $exception
     * @param string|null $statusCode
     * @param string|null $reasonPhrase
     * @return string
     */
    public function handle(Throwable $exception, string $statusCode = null, string $reasonPhrase = null): string
    {
        if ((file_exists(PROJECT_SOURCE_ROOT . 'view/ErrorView.php')) === false) {
            $this->view->setBasePath(__DIR__ . '/../../Http/View');
        }

        if ($exception instanceof HttpException) {
            $errorView = $this->getHttpErrorView($exception);
        }

        if ($exception instanceof Throwable) {
            $errorView = $this->getHttpErrorView($exception, $statusCode, $reasonPhrase);
        }

        return $errorView;
    }

    /**
     * @param HttpException $exception
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
                'xdebugTag' => $this->logger->getXDebugTag(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'stackTrace' => explode(PHP_EOL, $exception->getTraceAsString()),
            ]);
        }

        return $this->view->render('ErrorView', $params);
    }

}