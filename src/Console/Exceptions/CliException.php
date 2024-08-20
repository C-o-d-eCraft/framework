<?php

namespace Craft\Console\Exceptions;

use Exception;

abstract class CliException extends Exception
{
    public function __construct(string $message, int $statusCode)
    {
        parent::__construct($message, $statusCode);
    }

    public function getType(): string
    {
        $childClassName = (new \ReflectionClass($this))->getShortName();

        return str_replace('CliException', '', $childClassName);
    }
}
