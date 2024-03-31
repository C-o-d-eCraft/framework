<?php

namespace Craft\Http\Exceptions;

class NotFoundHttpException extends HttpException
{
    public function __construct(string $message = 'Страница не найдена!', int $statusCode = 404)
    {
        parent::__construct($message, $statusCode);
    }
}
