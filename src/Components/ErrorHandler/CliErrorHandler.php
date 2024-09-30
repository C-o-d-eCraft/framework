<?php

namespace Craft\Components\ErrorHandler;

use Craft\Components\DebugTag\DebugTagStorage;
use Craft\Contracts\ErrorHandlerInterface;
use Throwable;

class CliErrorHandler implements ErrorHandlerInterface
{
    public function __construct(private DebugTagStorage $debugTagStorage) { }

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

        $params = array_merge($params, [
            'xdebugTag' => $this->debugTagStorage->getTag(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'stackTrace' => $exception->getTraceAsString(),
        ]);

        $message = "Сообщение об ошибке:" . PHP_EOL . implode(PHP_EOL, array_map(
                fn($key, $value) => "$key: $value",
                array_keys($params),
                $params
            )) . PHP_EOL;

        return $message;
    }
}
