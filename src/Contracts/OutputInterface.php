<?php

namespace Craft\Contracts;

interface OutputInterface
{
    /**
     * Выводит результат в консоль
     *
     * @param string $result
     *
     * @return void
     */
    public function stdout(string $result): void;

    /**
     * Выводит информационное сообщение в консоль
     *
     * @param string $result
     *
     * @return void
     */
    public function info(string $result): void;

    /**
     * Выводит сообщение об ошибке в консоль.
     *
     * @param string $result
     *
     * @return void
     */
    public function warning(string $result): void;

    /**
     * Выводит сообщение об успешном выполнении в консоль.
     *
     * @param string $result
     *
     * @return void
     */
    public function success(string $result): void;

    public function primary(string $result): void;

    public function error(string $result): void;

    public function getStatusCode(): int;

    public function getMessage(): string;
}
