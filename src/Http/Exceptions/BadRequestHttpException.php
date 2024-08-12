<?php

namespace Craft\Http\Exceptions;

class BadRequestHttpException
{
    public function __construct(string $message = 'Некорректный запрос!', int $statusCode = 400)
    {
        parent::__construct($message, $statusCode);
    }
}
