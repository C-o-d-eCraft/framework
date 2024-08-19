<?php

namespace Craft\Console\Exceptions;


class CommandInterruptedException extends CliException
{
    public function __construct(string $message = 'Дальнейшее выполнение команды прервано, успешное завершение', int $statusCode = 200)
    {
        parent::__construct($message, $statusCode);
    }
}