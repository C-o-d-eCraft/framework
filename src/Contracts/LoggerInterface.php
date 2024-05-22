<?php

namespace Craft\Contracts;

interface LoggerInterface
{
    public function emergency($message, array $context = [], array $extras = []): void;
    public function alert($message, array $context = [], array $extras = []): void;
    public function critical($message, array $context = [], array $extras = []): void;
    public function error($message, array $context = [], array $extras = []): void;
    public function warning($message, array $context = [], array $extras = []): void;
    public function notice($message, array $context = [], array $extras = []): void;
    public function info($message, array $context = [], array $extras = []): void;
    public function debug($message, array $context = [], array $extras = []): void;
}
