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

    public function emergency(string $message): void
    {
        $this->log(LogLevel::EMERGENCY->value, $message);
    }

    public function alert(string $message): void
    {
        $this->log(LogLevel::ALERT->value, $message);
    }

    public function critical(string $message): void
    {
        $this->log(LogLevel::CRITICAL->value, $message);
    }

    public function error(string $message): void
    {
        $this->log(LogLevel::ERROR->value, $message);
    }

    public function warning(string $message): void
    {
        $this->log(LogLevel::WARNING->value, $message);
    }

    public function notice(string $message): void
    {
        $this->log(LogLevel::NOTICE->value, $message);
    }

    public function info(string $message): void
    {
        $this->log(LogLevel::INFO->value, $message);
    }

    public function debug(string $message): void
    {
        $this->log(LogLevel::DEBUG->value, $message);
    }

    private function log(string $level, string $message): void
    {
        $logMessage = $this->formatMessage($level, $message);

        $this->writeLogToFile($logMessage);
        $this->writeLogToStdout($logMessage);
    }

    private function formatMessage(string $level, string $message): string
    {
        $loggingState = $this->logStateProcessor->process($level, $message);

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
