<?php

namespace Craft\Http\ResponseTypes;

use Craft\Http\ResponseTypes\JsonResponse;

class CreateResponse extends JsonResponse
{
    public function __construct(public int $statusCode = 201)
    {
        $this->setStatusCode($this->statusCode);
    }
}
