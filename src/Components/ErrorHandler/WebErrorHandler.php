<?php

namespace Craft\Components\ErrorHandler;

use JetBrains\PhpStorm\NoReturn;
use Throwable;

class WebErrorHandler
{
    /**
     * @param string $massege
     */
    const LOG_LEVEL_DEBUG = 0;

    /**
    * @param string $massege
    */
    const LOG_LEVEL_INFO = 1;

    /**
     * @param string $massege
     */
    const LOG_LEVEL_WARNING = 2;

    /**
     * @param string $massege
     */
    const LOG_LEVEL_ERROR = 3;

    public function __construct(
        private readonly int    $logLevel,
        private readonly string $logFilePath
    ) { }

    /**
     * @var array
     */
    private array $errorTypes = [
        E_ERROR => 'Error',
        E_CORE_ERROR => 'Error',
        E_COMPILE_ERROR => 'Error',
        E_USER_ERROR => 'Error',
        E_WARNING => 'Warning',
        E_CORE_WARNING => 'Warning',
        E_COMPILE_WARNING => 'Warning',
        E_USER_WARNING => 'Warning',
        E_NOTICE => 'Notice',
        E_USER_NOTICE => 'Notice'
    ];

    /**
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
    #[NoReturn] public function handle($errno, $errstr, $errfile, $errline): void
    {
        $errorType = $this->errorTypes[$errno] ?? 'Unknown';

        $errorMessage = "[" . date('Y-m-d H:i:s') . "] {$errorType}: {$errstr} in {$errfile} on line {$errline}";

        $this->logError($errorMessage);

        echo $errorMessage;

        die();
    }

    /**
     * @param Throwable $exception
     * @return void
     */
    #[NoReturn] public function handleException(Throwable $exception): void
    {
        $errorMessage = "[" . date('Y-m-d H:i:s') . "] Exception: {$exception->getMessage()} in {$exception->getFile()} on line {$exception->getLine()}";

        $this->logError($errorMessage);

        echo $errorMessage;

        die();
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
