<?php

namespace Craft\Contracts;

interface LoggerInterface
{
    /**
     * @param string|\Throwable $message
     * @param string $category
     * @param string $context
     * @param mixed|null $extras
     * @param string $xDebugTag
     * @return void
     */
    public function writeLog(
        string|\Throwable $message,
        string $category,
        string $context,
        mixed $extras = null,
        string $xDebugTag = ''
    ): void;
}
