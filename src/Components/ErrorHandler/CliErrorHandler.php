<?php

namespace Craft\Components\ErrorHandler;

use Craft\Contracts\LoggerInterface;
use Throwable;

class CliErrorHandler
{
    /**
     * @param LoggerInterface $logger
     */
    public function __construct(private LoggerInterface $logger) { }

    /**
     * @param Throwable $exception
     * @param string|null $statusCode
     * @param string|null $reasonPhrase
     * @return string
     */
    public function handle(Throwable $exception, string $statusCode = null, string $reasonPhrase = null): string
    {
        $params = [
            'reasonPhrase' => $reasonPhrase ?? $exception->getMessage(),
        ];

        if (getenv('ENV') === 'development') {
            $params = array_merge($params, [
                'xdebugTag' => defined('X_DEBUG_TAG') ? X_DEBUG_TAG : null,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'stackTrace' => $exception->getTraceAsString(),
            ]);
        }

        $message = "Сообщение об ошибке:" . PHP_EOL . implode(PHP_EOL, array_map(
                fn($key, $value) => "$key: $value",
                array_keys($params),
                $params
            )) . PHP_EOL;

        $this->logger->error($exception->getMessage(), ['exception' => $exception]);

        return $message;
    }
}
