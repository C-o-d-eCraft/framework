<?php

namespace Craft\Contracts;

interface LoggerInterface
{
    /**
     * @param mixed $message
     * @return void
     */
    public function emergency(mixed $message): void;

    /**
     * @param mixed $message
     * @return void
     */
    public function alert(mixed $message): void;

    /**
     * @param mixed $message
     * @return void
     */
    public function critical(mixed $message): void;

    /**
     * @param mixed $message
     * @return void
     */
    public function error(mixed $message): void;

    /**
     * @param mixed $message
     * @return void
     */
    public function warning(mixed $message): void;

    /**
     * @param mixed $message
     * @return void
     */
    public function notice(mixed $message): void;

    /**
     * @param mixed $message
     * @return void
     */
    public function info(mixed $message): void;

    /**
     * @param mixed $message
     * @return void
     */
    public function debug(mixed $message): void;
}
