<?php

namespace Framework\Http\Exceptions;

class HttpException extends \Exception
{
    public function __construct($message, $code)
    {
        parent::__construct($message, $code);
    }
}
