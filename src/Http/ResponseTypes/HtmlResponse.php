<?php

namespace Craft\Http\ResponseTypes;

use Craft\Http\Message\Response;
use Craft\Http\Message\Stream;

class HtmlResponse extends Response 
{
    /**
     * @param string $result
     * @return void
     */
    public function setHtmlBody(string $result): void
    {
        $this->body = new Stream($result);
    }
}
