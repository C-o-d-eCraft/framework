<?php

namespace Craft\Http\Exceptions;

class HttpLogicException extends HttpException
{
    public function __construct(string $message = 'Логическая ошибка!', int $statusCode = 400)
    {
        parent::__construct($message, $statusCode);
    }
}