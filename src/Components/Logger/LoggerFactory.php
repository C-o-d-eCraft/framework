<?php

namespace Craft\Components\Logger;

use Craft\Contracts\RequestInterface;

class LoggerFactory
{
    /**
     * @param RequestInterface $request
     */
    public function __construct(private readonly RequestInterface $request) {   }
    
    /**
     * @param string $indexName
     * @param string $xDebugTag
     * @return Logger
     */
    public function create(): Logger
    {
        $xDebugTag = $this->request->getHeaderLine('x-debug-tag');
        
        if ($xDebugTag === '') {
            $xDebugTag = md5('x-debug-tag-' . getenv('INDEX_NAME') . '-' . rand(1e9, 9e9) . '-' . gethostname() . time());
        }

        return new Logger(getenv('INDEX_NAME'), $xDebugTag);
    }
}
