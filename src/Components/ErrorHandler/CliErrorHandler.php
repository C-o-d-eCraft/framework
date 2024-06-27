<?php

namespace Craft\Components\ErrorHandler;

use Throwable;

class CliErrorHandler
{
    public function __construct(private ?string $environmentMode = null) { }

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

        if ($this->environmentMode === 'development') {
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

        return $message;
    }
}
