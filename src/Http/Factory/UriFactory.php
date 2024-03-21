<?php

namespace Craft\Http\Factory;

use Craft\Contracts\UriInterface;
use Craft\Http\Message\Uri;

class UriFactory
{
    /**
     * @return UriInterface
     */
    public static function createUri(): UriInterface
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $path = parse_url($uri, PHP_URL_PATH);
        $query = parse_url($uri, PHP_URL_QUERY) ?? '';
        $queryParams = [];
        parse_str($query, $queryParams);
        $fragment = parse_url($uri, PHP_URL_FRAGMENT) ?? '';
        $port = $_SERVER['SERVER_PORT'] ?? null;

        return new Uri($uri, $scheme, $host, $path, $query, $queryParams, $fragment, $port);
    }
}
