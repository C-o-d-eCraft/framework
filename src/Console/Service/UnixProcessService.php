<?php

namespace Craft\Console\Service;

use Craft\Contracts\UnixProcessServiceInterface;

class UnixProcessService implements UnixProcessServiceInterface
{
    /**
     * Клонирует текущий процесс, и возвращает PID дочернего.
     * В случае ошибки вернет -1
     *
     * @return int
     */
    public function fork(): int
    {
      return pcntl_fork();
    }

    /**
     * Возвращает PID текущего процесса.
     *
     * @return int
     */
    public function getPid(): int
    {
        return posix_getpid();
    }

    /**
     * Сделать текущий процесс лидером сеанса
     * В случае ошибки вернет -1
     *
     * @return int
     */
    public function setSid(): int
    {
        return posix_setsid();
    }

    /**
     * Закрыть файловые дескрипторы stdin, stdout и stderr для дочернего процесса
     *
     * @return void
     */
    public function descriptionClose(): void
    {
        fclose(STDIN);
        fclose(STDOUT);
        fclose(STDERR);
    }

    /**
     * Закрыть файловые дескрипторы stdin, stdout и stderr для дочернего процесса
     *
     * @return void
     */
    public function descriptionOpenDevNull(): void
    {
        fopen('/dev/null', 'r');
        fopen('/dev/null', 'a');
        fopen('/dev/null', 'a');
    }
}
