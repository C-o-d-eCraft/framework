<?php

namespace Craft\Http\ResponseTypes;

use Craft\Http\Message\Response;

class DeleteResponse extends Response
{
    public function __construct(int $statusCode = 204)
    {
        $this->statusCode = $statusCode;
    }
}
