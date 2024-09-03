<?php

namespace Craft\Http\ResponseTypes;

use Craft\Http\Message\Response;
use Craft\Http\Message\Stream;

class JsonResponse extends Response
{
    /**
     * @param array $data
     * @return void
     */
    public function setJsonBody(array $data): void
    {
        $this->body = new Stream(json_encode($data));
    }
}
