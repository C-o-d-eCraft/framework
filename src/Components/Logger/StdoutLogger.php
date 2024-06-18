<?php

namespace Craft\Components\Logger;

use Craft\Components\Logger\StateProcessor\LogStateProcessor;
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

    public function emergency($message, array $context = [], $extras = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context, $extras);
    }

    public function alert($message, array $context = [], $extras = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context, $extras);
    }

    public function critical($message, array $context = [], $extras = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context, $extras);
    }

    public function error($message, array $context = [], $extras = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context, $extras);
    }

    public function warning($message, array $context = [], $extras = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context, $extras);
    }

    public function notice($message, array $context = [], $extras = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context, $extras);
    }

    public function info($message, array $context = [], $extras = []): void
    {
        $this->log(LogLevel::INFO, $message, $context, $extras);
    }

    public function debug($message, array $context = [], $extras = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context, $extras);
    }

    public function log($level, $message, array $context = [], $extras = []): void
    {
        if ($this->isValidLogLevel($level) === false) {
            throw new \InvalidArgumentException("Несуществующий уровень логгирования: $level");
        }

        $logMessage = $this->formatMessage($level, $message, $context, $extras);

        $this->writeLogToFile($logMessage);
        $this->writeLogToStdout($logMessage);
    }

    private function isValidLogLevel($level): bool
    {
        return in_array($level, LogLevel::getLevel());
    }

    private function formatMessage($level, $message, $context, $extras): string
    {
        $loggingState = $this->logStateProcessor->process($level, $message, $context, $extras);

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
