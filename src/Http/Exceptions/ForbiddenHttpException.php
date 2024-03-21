<?php

namespace Craft\Http\Exceptions;

class ForbiddenHttpException extends HttpException
{
    public function __construct(string $message = 'Доступ запрещен!')
    {
        parent::__construct($message, 403);
    }
}
