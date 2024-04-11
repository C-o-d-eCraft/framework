<?php

namespace Craft\Http\Exceptions;

class ForbiddenHttpException extends HttpException
{
    public function __construct(string $message = 'Доступ запрещен!', int $statusCode = 403)
    {
        parent::__construct($message, $statusCode);
    }
}
