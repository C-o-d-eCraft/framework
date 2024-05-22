<?php

namespace Craft\Contracts;

interface LogStateProcessorInterface
{
    /**
     * @param string $level
     * @param string $message
     * @param array $context
     * @return object
     */
    public function process(string $level, string $message, array $context): object;
}