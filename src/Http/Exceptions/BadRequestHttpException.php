<?php

namespace Framework\Http\Exceptions;

class BadRequestHttpException extends HttpException
{
    public function __construct(string $message = 'Некорректный запрос!')
    {
         parent::__construct($message, 400);
    }
}
