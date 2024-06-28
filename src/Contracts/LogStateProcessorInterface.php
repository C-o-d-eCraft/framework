<?php

namespace Craft\Contracts;

interface LogStateProcessorInterface
{
    /**
     * @param string $level
     * @param string $message
     *
     * @return object
     */
    public function process(string $level, string $message): object;
}
