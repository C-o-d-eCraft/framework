<?php

namespace Craft\Contracts;

interface LoggerInterface
{
    public function emergency(string $message, array $context = [], array $extras = []): void;
    public function alert(string $message, array $context = [], array $extras = []): void;
    public function critical(string $message, array $context = [], array $extras = []): void;
    public function error(string $message, array $context = [], array $extras = []): void;
    public function warning(string $message, array $context = [], array $extras = []): void;
    public function notice(string $message, array $context = [], array $extras = []): void;
    public function info(string $message, array $context = [], array $extras = []): void;
    public function debug(string $message, array $context = [], array $extras = []): void;
}
