<?php

namespace Craft\Http\Exceptions;

class NotFoundHttpException extends HttpException
{
    public function __construct(string $message = 'Страница не найдена!')
    {
        parent::__construct($message, 404);
    }
}
