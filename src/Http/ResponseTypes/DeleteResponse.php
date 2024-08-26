<?php

namespace Craft\Http\ResponseTypes;

use Craft\Http\ResponseTypes\JsonResponse;

class DeleteResponse extends JsonResponse
{
    public function __construct(public int $statusCode = 204)
    {
        $this->setStatusCode($this->statusCode);
    }
}
