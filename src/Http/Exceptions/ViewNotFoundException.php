<?php

namespace Craft\Http\Exceptions;

class ViewNotFoundException extends \Exception
{
    public function __construct(string $message = 'Представление не найдено!', int $statusCode = 404)
    {
        parent::__construct($message, $statusCode);
    }
}
