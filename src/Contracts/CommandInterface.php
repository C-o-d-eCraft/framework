<?php

namespace Craft\Contracts;

interface CommandInterface
{
    /**
     * @return string
     */
    public static function getCommandName(): string;

    /**
     * @return string
     */
    public static function getDescription(): string;

    /**
     * - argumentName: (string) Имя аргумента команды.
     * - required: (bool) Указывает, является ли аргумент обязательным.
     * - defaultValue: (mixed) Значение по умолчанию для необязательного аргумента.
     * - comment: (string) Описание аргумента.
     *
     * @return array Ассоциативный массив с параметрами команды.
     */
    public static function getCommandParam(): array;


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output): void;
}
