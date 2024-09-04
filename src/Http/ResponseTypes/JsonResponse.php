<?php

namespace Craft\Http\ResponseTypes;

use Craft\Http\Message\Response;
use Craft\Http\Message\Stream;

class JsonResponse extends Response
{
    public function __construct(array $data)
    {
        $this->setJsonBody($data);
    }

    /**
     * @param array $data
     * @return void
     */
    private function setJsonBody(array $data): void
    {
        $this->body = new Stream(json_encode($data));
    }
}
