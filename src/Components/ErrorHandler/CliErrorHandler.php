<?php

namespace Craft\Components\ErrorHandler;

use JetBrains\PhpStorm\NoReturn;
use Throwable;

class CliErrorHandler
{
    /**
     * @param string $message
     */
    const LOG_LEVEL_DEBUG = 0;

    /**
     * @param string $message
     */
    const LOG_LEVEL_INFO = 1;

    /**
     * @param string $message
     */
    const LOG_LEVEL_WARNING = 2;

    /**
     * @param string $message
     */
    const LOG_LEVEL_ERROR = 3;

    public function __construct(
        private readonly int    $logLevel,
        private readonly string $logFilePath
    ) { }

    /**
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     * @return void
     */
    #[NoReturn] public function handle($errno, $errstr, $errfile, $errline): void
    {
        $errorType = $this->getErrorTypeString($errno);
        $errorMessage = sprintf("[%s] %s: %s in %s on line %d", date('Y-m-d H:i:s'), $errorType, $errstr, $errfile, $errline);

        $this->logError($errorMessage);

        echo $errorMessage . PHP_EOL;
        exit(1);
    }

    /**
     * @param Throwable $exception
     * @return void
     */
    #[NoReturn] public function handleException(Throwable $exception): void
    {
        $errorMessage = sprintf("[%s] Exception: %s in %s on line %d", date('Y-m-d H:i:s'), $exception->getMessage(), $exception->getFile(), $exception->getLine());

        $this->logError($errorMessage);

        echo $errorMessage . PHP_EOL;
        exit(1);
    }

    /**
     * @param int $errno
     * @return string
     */
    private function getErrorTypeString(int $errno): string
    {
        $errorTypes = [
            E_ERROR             => 'Error',
            E_CORE_ERROR        => 'Error',
            E_COMPILE_ERROR     => 'Error',
            E_USER_ERROR        => 'Error',
            E_WARNING           => 'Warning',
            E_CORE_WARNING      => 'Warning',
            E_COMPILE_WARNING   => 'Warning',
            E_USER_WARNING      => 'Warning',
            E_NOTICE            => 'Notice',
            E_USER_NOTICE       => 'Notice',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED        => 'Deprecated',
            E_USER_DEPRECATED   => 'User Deprecated'
        ];

        return $errorTypes[$errno] ?? 'Unknown';
    }

    /**
     * @param string $errorMessage
     * @return void
     */
    private function logError(string $errorMessage): void
    {
        if ($this->logLevel >= self::LOG_LEVEL_ERROR) {
            file_put_contents($this->logFilePath, $errorMessage . PHP_EOL, FILE_APPEND);
        }
    }
}
