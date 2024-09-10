<?php

namespace Craft\Components\Logger;

use Craft\Contracts\LoggerInterface;
use Craft\Contracts\LogStateProcessorInterface;

abstract class AbstractLogger implements LoggerInterface
{
    public function __construct(private LogStateProcessorInterface $logStateProcessor) { }
    
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

    protected function log(string $level, mixed $message): void
    {
        $logMessage = $this->formatMessage($level, $message);
        $this->writeLog($logMessage);
    }
    
    protected function formatMessage(string $level, mixed $message): string
    {
        $loggingState = $this->logStateProcessor->process($level, $message);

        return json_encode((array)$loggingState, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    abstract protected function writeLog(mixed $logMessage): void;
}
