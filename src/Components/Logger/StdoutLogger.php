<?php

namespace Craft\Components\Logger;

use Craft\Components\Logger\StateProcessor\LogStateProcessor;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\LoggerInterface;

class StdoutLogger implements LoggerInterface
{
    private string $logFilePath;

    public function __construct(private LogStateProcessor $logStateProcessor)
    {
        $logDir = PROJECT_ROOT . '/runtime/app-logs';

        if (is_dir($logDir) === false) {
            mkdir($logDir, 0777, true);
        }

        $this->logFilePath = $logDir . '/app-log-' . date('Y-m-d') . '.log';

        if (file_exists($this->logFilePath) === false) {
            touch($this->logFilePath);
        }
    }

    public function emergency(mixed $message): void
    {
        $this->log(LogLevel::EMERGENCY->value, $message);
    }

    public function alert(mixed $message): void
    {
        $this->log(LogLevel::ALERT->value, $message);
    }

    public function critical(mixed $message): void
    {
        $this->log(LogLevel::CRITICAL->value, $message);
    }

    public function error(mixed $message): void
    {
        $this->log(LogLevel::ERROR->value, $message);
    }

    public function warning(mixed $message): void
    {
        $this->log(LogLevel::WARNING->value, $message);
    }

    public function notice(mixed $message): void
    {
        $this->log(LogLevel::NOTICE->value, $message);
    }

    public function info(mixed $message): void
    {
        $this->log(LogLevel::INFO->value, $message);
    }

    public function debug(mixed $message): void
    {
        $this->log(LogLevel::DEBUG->value, $message);
    }

    private function log(string $level, mixed $message): void
    {
        $logMessage = $this->formatMessage($level, $message);

        $this->writeLogToFile($logMessage);
        $this->writeLogToStdout($logMessage);
    }

    private function formatMessage(string $level, mixed $message): string
    {
        $loggingState = $this->logStateProcessor->process($level, $message);

        return json_encode((array)$loggingState, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    private function writeLogToFile(mixed $logMessage): void
    {
        file_put_contents($this->logFilePath, $logMessage . PHP_EOL, FILE_APPEND);
    }

    private function writeLogToStdout(mixed $logMessage): void
    {
        $fileHandle = fopen('php://stdout', 'w');

        if ($fileHandle === false) {
            error_log("Не удалось открыть php://stdout для записи");
        }

        fwrite($fileHandle, $logMessage);
        fclose($fileHandle);
    }
}
