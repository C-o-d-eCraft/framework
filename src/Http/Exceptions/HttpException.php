<?php

namespace Craft\Http\Exceptions;

class HttpException extends \Exception
{
    public function __construct(string $message = 'Некорректный запрос!', int $statusCode = 400)
    {
        parent::__construct($message, $statusCode);
    }

    public function getType(): string
    {
        $calledClassName = (new \ReflectionClass($this))->getShortName();

        if ($calledClassName === 'HttpException') {
            return 'BadRequest';
        }

        return str_replace('HttpException', '', $baseClassName);
    }
}
