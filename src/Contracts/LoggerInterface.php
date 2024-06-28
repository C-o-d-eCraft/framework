<?php

namespace Craft\Contracts;

interface LoggerInterface
{
    public function emergency(mixed $message): void;
    public function alert(mixed $message): void;
    public function critical(mixed $message): void;
    public function error(mixed $message): void;
    public function warning(mixed $message): void;
    public function notice(mixed $message): void;
    public function info(mixed $message): void;
    public function debug(mixed $message): void;
}
