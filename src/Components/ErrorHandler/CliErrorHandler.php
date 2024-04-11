<?php

namespace Craft\Components\ErrorHandler;

use Craft\Contracts\LoggerInterface;
use Throwable;


class CliErrorHandler
{
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(
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
        $params = [
            'reasonPhrase' => MessageEnum::INTERNAL_SERVER_ERROR ?? $exception->getMessage(),
        ];

        if (getenv('ENV') === 'development') {
            $params = array_merge($params, [
                'xdebugTag' => $this->logger->getXDebugTag(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'stackTrace' =>$exception->getTraceAsString(),
            ]);
        }

        $message = implode(PHP_EOL, $params);

        $message = "Сообщение об ошибке:" . PHP_EOL . $message . PHP_EOL;

        return $message;
    }
}