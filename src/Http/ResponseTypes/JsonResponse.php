<?php

namespace Craft\Http\ResponseTypes;

use Craft\Http\Message\Response;
use Craft\Http\Message\Stream;

class JsonResponse extends Response
{
    /**
     * @param array $result
     * @return void
     */
    public function setJsonBody(array $result): void
    {
        $this->body = new Stream(json_encode($result));
    }
}
