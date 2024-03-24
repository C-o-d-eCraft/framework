<?php

namespace Craft\Http\Factory;

use Craft\Contracts\RequestInterface;
use Craft\Http\Message\Request;
use Craft\Http\Message\Stream;

class RequestFactory
{
    /**
     * @return RequestInterface
     */
    public static function createRequest(): RequestInterface
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $headers = self::getHeadersFromGlobals();

        $protocolVersion = isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '';
        $body = new Stream(fopen('php://input', 'r'));

        return new Request($method, UriFactory::createUri(), $headers, $body, $protocolVersion ?: 'HTTP/1.1');
    }

    /**
     * @return array
     */
    private static function getHeadersFromGlobals(): array
    {
        $headers = [];

        foreach ($_SERVER as $name => $value) {
            if (str_starts_with($name, 'HTTP_')) {
                $name = str_replace('HTTP_', '', $name);
                $name = str_replace('_', '-', $name);
                $headers[$name] = $value;
            }
        }

        return $headers;
    }
}
