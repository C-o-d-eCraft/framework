<?php

namespace Craft\Components\Logger;

use Craft\Components\Logger\StateProcessor\LogStateProcessor;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\LoggerInterface;

class StdoutLogger implements LoggerInterface
{
    private LogStateProcessor $logStateProcessor;
    private string $logFilePath;

    public function __construct()
    {
        if (empty(getenv('INDEX_NAME'))) {
            throw new \InvalidArgumentException('Не задано имя приложения. Внесите доработку в конфигурацию приложения');
        }
        
        $this->logStateProcessor = new LogStateProcessor(getenv('INDEX_NAME'));

        $logDir = PROJECT_ROOT . '/runtime/app-logs';

        if (is_dir($logDir) === false) {
            mkdir($logDir, 0777, true);
        }

        $this->logFilePath = $logDir . '/app-log-' . date('Y-m-d') . '.log';

        if (file_exists($this->logFilePath) === false) {
            touch($this->logFilePath);
        }
    }

    public function emergency(string $message, array $context = [], array $extras = []): void
    {
        $this->log(LogLevel::EMERGENCY->value, $message, $context, $extras);
    }

    public function alert(string $message, array $context = [], array $extras = []): void
    {
        $this->log(LogLevel::ALERT->value, $message, $context, $extras);
    }

    public function critical(string $message, array $context = [], array $extras = []): void
    {
        $this->log(LogLevel::CRITICAL->value, $message, $context, $extras);
    }

    public function error(string $message, array $context = [], array $extras = []): void
    {
        $this->log(LogLevel::ERROR->value, $message, $context, $extras);
    }

    public function warning(string $message, array $context = [], array $extras = []): void
    {
        $this->log(LogLevel::WARNING->value, $message, $context, $extras);
    }

    public function notice(string $message, array $context = [], array $extras = []): void
    {
        $this->log(LogLevel::NOTICE->value, $message, $context, $extras);
    }

    public function info(string $message, array $context = [], array $extras = []): void
    {
        $this->log(LogLevel::INFO->value, $message, $context, $extras);
    }

    public function debug(string $message, array $context = [], array $extras = []): void
    {
        $this->log(LogLevel::DEBUG->value, $message, $context, $extras);
    }

    public function log(string $level, string $message, array $extras = []): void
    {
        $logMessage = $this->formatMessage($level, $message, $extras);

        $this->writeLogToFile($logMessage);
        $this->writeLogToStdout($logMessage);
    }

    private function formatMessage(string $level, string $message, array $extras = []): string
    {
        $loggingState = $this->logStateProcessor->process($level, $message, $extras);

        return json_encode((array)$loggingState, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    private function writeLogToFile(string $logMessage): void
    {
        file_put_contents($this->logFilePath, $logMessage . PHP_EOL, FILE_APPEND);
    }

    private function writeLogToStdout(string $logMessage): void
    {
        $fileHandle = fopen('php://stdout', 'w');

        if ($fileHandle === false) {
            error_log("Не удалось открыть php://stdout для записи");
        }

        fwrite($fileHandle, $logMessage);
        fclose($fileHandle);
    }
}
