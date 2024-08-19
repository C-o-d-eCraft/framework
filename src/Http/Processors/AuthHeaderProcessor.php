<?php

namespace Craft\Http\Processors;

use Craft\Contracts\RequestInterface;

class AuthHeaderProcessor
{
    /**
     * @param array $headers
     * @param array $params
     * @return array
     */
    public function processAuthHeader(array $headers, array $params): array
    {
        if (isset($headers['X-BASE-AUTH']) === true && $headers['X-BASE-AUTH'] !== null) {
            $params['token'] = $headers['X-BASE-AUTH'];
        }

        return $params;
    }
}
