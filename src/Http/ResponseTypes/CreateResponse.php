<?php

namespace Craft\Http\ResponseTypes;

use Craft\Http\Message\Response;

class CreateResponse extends Response
{
    public function __construct(int $statusCode = 201)
    {
        $this->statusCode = $statusCode;
    }
}
