<?php

namespace Craft\Http\Message;

use Craft\Contracts\UriInterface;

class Uri implements UriInterface
{
    /**
     * @var string
     */
    public string $uri;

    /**
     * @var string
     */
    public string $scheme;

    /**
     * @var string
     */
    public string $host;

    /**
     * @var string
     */
    public string $path;

    /**
     * @var string
     */
    public string $query;

    /**
     * @var array
     */
    public array $queryParams;

    /**
     * @var string
     */
    public string $fragment;

    /**
     * @var int|null
     */
    public ?int $port;

    /**
     * @param string $uri
     * @param string $scheme
     * @param string $host
     * @param string $path
     * @param string $query
     * @param array $queryParams
     * @param string $fragment
     * @param int|null $port
     */
    public function __construct(
        string $uri = '',
        string $scheme = '',
        string $host = '',
        string $path = '',
        string $query = '',
        array $queryParams = [],
        string $fragment = '',
        ?int $port = null,
    )
    {
        $this->uri = $uri;
        $this->scheme = $scheme;
        $this->path = $path;
        $this->host = $host;
        $this->query = $query;
        $this->queryParams = $queryParams;
        $this->fragment = $fragment;
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $uriString = $this->scheme !== '' ? $this->scheme . '://' : '';

        $uriString .= $this->getAuthority();

        if ($this->path !== '') {
            $uriString .= '/' . ltrim($this->path, '/');
        }

        if ($this->query !== '') {
            $uriString .= '?' . $this->query;
        }

        if ($this->fragment !== '') {
            $uriString .= '#' . $this->fragment;
        }

        return $uriString;
    }

    /**
     * @return string
     */
    public function getStringUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * @param array $param
     * @return void
     */
    public function addQueryParams(array $param): void
    {
        foreach ($param as $key => $value) {
            if (in_array($key, array_keys($this->queryParams), true) === false) {
                $this->queryParams[$key] = $value;
            }
        }
    }

    /**
     * @return string
     */
    public function getFragment(): string
    {
        return $this->fragment;
    }

    /**
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * @param string $host
     * @return $this
     */
    public function withHost(string $host): static
    {
        $newUri = clone $this;

        $newUri->host = $host;

        return $newUri;
    }

    /**
     * @param string $scheme
     * @return $this
     */
    public function withScheme(string $scheme): static
    {
        $newUri = clone $this;

        $newUri->scheme = $scheme;

        return $newUri;
    }

    /**
     * @param int|null $port
     * @return $this
     */
    public function withPort(?int $port): static
    {
        $newUri = clone $this;

        $newUri->port = $port;

        return $newUri;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function withPath(string $path): static
    {
        $newUri = clone $this;

        $newUri->path = $path;

        return $newUri;
    }

    /**
     * @param string $query
     * @return $this
     */
    public function withQuery(string $query): static
    {
        $newUri = clone $this;

        $newUri->query = $query;

        return $newUri;
    }

    /**
     * @param string $fragment
     * @return $this
     */
    public function withFragment(string $fragment): static
    {
        $newUri = clone $this;

        $newUri->fragment = $fragment;

        return $newUri;
    }

    /**
     * @return string
     */
    public function getAuthority(): string
    {
        $authority = $this->host;

        if ($this->port !== null) {
            $authority .= ':' . $this->port;
        }

        return $authority;
    }

    /**
     * заглушка
     *
     * @return string
     */
    public function getUserInfo(): string
    {
        return '';
    }

    /**
     *  заглушка
     *
     * @param string $user
     * @param string|null $password
     * @return $this
     */
    public function withUserInfo(string $user, string $password = null): static
    {
        return $this;
    }
}
