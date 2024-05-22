<?php

namespace Craft\Components\Logger;

use Craft\Components\Logger\StateProcessor\LogStateProcessor;

class StdoutLogger implements LoggerInterface
{
    private LogStateProcessor $logStateProcessor;

    public function __construct()
    {
        if (empty(getenv('INDEX_NAME'))) {
            throw new \InvalidArgumentException('Не задано имя приложения. Внесите доработку в конфигурацию приложения');
        }

        $this->logStateProcessor = new LogStateProcessor(getenv('INDEX_NAME'));
    }

    public function emergency($message, array $context = []): void
    {
        $this->log(LogLevel::EMERGENCY, $message, $context);
    }

    public function alert($message, array $context = []): void
    {
        $this->log(LogLevel::ALERT, $message, $context);
    }

    public function critical($message, array $context = []): void
    {
        $this->log(LogLevel::CRITICAL, $message, $context);
    }

    public function error($message, array $context = []): void
    {
        $this->log(LogLevel::ERROR, $message, $context);
    }

    public function warning($message, array $context = []): void
    {
        $this->log(LogLevel::WARNING, $message, $context);
    }

    public function notice($message, array $context = []): void
    {
        $this->log(LogLevel::NOTICE, $message, $context);
    }

    public function info($message, array $context = []): void
    {
        $this->log(LogLevel::INFO, $message, $context);
    }

    public function debug($message, array $context = []): void
    {
        $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, array $context = []): void
    {
        if ($this->isValidLogLevel($level) === false) {
            throw new \InvalidArgumentException("Несуществующий уровень логгирования: $level");
        }

        $logMessage = $this->formatMessage($level, $message, $context);

        $fileHandle = fopen('php://stdout', 'w');

        if ($fileHandle) {
            fwrite($fileHandle, $logMessage);
            fclose($fileHandle);

            return;
        }

        error_log("Не удалось открыть php://stdout для записи");
    }

    private function isValidLogLevel($level): bool
    {
        return in_array($level, LogLevel::getLevel());
    }

    private function formatMessage($level, $message, $context): string
    {
        $loggingState = $this->logStateProcessor->process($level, $message, $context);

        return json_encode((array)$loggingState);
    }
}