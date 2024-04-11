<?php

namespace Craft\Http\Exceptions;

class NotAuthorizedHttpException extends HttpException
{
    public function __construct(string $message = 'Авторизация не пройдена!', int $statusCode = 401)
    {
        parent::__construct($message, $statusCode);
    }
}