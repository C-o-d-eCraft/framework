<?php

namespace Craft\Http\Exceptions;

class BadRequestHttpException extends HttpException
{
    public function __construct(string $message = 'Некорректный запрос!', int $statusCode = 400)
    {
         parent::__construct($message, $statusCode);
    }
}
