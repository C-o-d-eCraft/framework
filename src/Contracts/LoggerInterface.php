<?php

namespace Craft\Contracts;

interface LoggerInterface
{
    public function emergency(string $message, array $extras = [], array $trace = []): void;
    public function alert(string $message, array $extras = [], array $trace = []): void;
    public function critical(string $message, array $extras = [], array $trace = []): void;
    public function error(string $message, array $extras = [], array $trace = []): void;
    public function warning(string $message, array $extras = [], array $trace = []): void;
    public function notice(string $message, array $extras = [], array $trace = []): void;
    public function info(string $message, array $extras = [], array $trace = []): void;
    public function debug(string $message, array $extras = [], array $trace = []): void;
}
