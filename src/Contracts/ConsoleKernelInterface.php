<?php

namespace Craft\Contracts;

interface ConsoleKernelInterface
{
    /**
     * Регистрирует пространства имен для поиска команд.
     *
     * @param array $commandNameSpaces Массив пространств имен для регистрации команд.
     */
    public function registerCommandNamespaces(array $commandNameSpaces): void;

    /**
     * Завершает выполнение приложения с указанным статусом завершения.
     *
     * @param int $exitStatus Код завершения выполнения приложения.
     */
    public function terminate(int $exitStatus): void;

    /**
     * Обрабатывает ввод с консоли и выполняет соответствующую команду.
     *
     * @return int Код завершения выполнения команды.
     */
    public function handle(): int;

    /**
     * Возвращает карту зарегистрированных консольных команд.
     *
     * @return array Массив, представляющий карту зарегистрированных консольных команд.
     */
    public function getCommandMap(): array;
}
