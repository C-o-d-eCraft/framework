<?php

namespace Craft\Contracts;

interface LoggerInterface
{
    /**
     * @param string|\Throwable $message
     * @param string $category
     * @param string $context
     * @param mixed|null $extras
     * @return void
     */
    public function writeLog(
        string|\Throwable $message,
        string $category,
        mixed $extras = null,
    ): void;
}
