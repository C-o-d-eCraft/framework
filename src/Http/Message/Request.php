<?php

namespace Craft\Http\Message;

use Craft\Contracts\RequestInterface;
use Craft\Contracts\StreamInterface;
use Craft\Contracts\UriInterface;

class Request extends Message implements RequestInterface
{
    /**
     * @var string
     */
    private string $method;

    /**
     * @var UriInterface
     */
    private UriInterface $uri;

    /**
     * @var array
     */
    private array $headers;

    /**
     * @var StreamInterface
     */
    private StreamInterface $body;

    /**
     * @var string
     */
    private string $protocolVersion;

    /**
     * @param string $method
     * @param UriInterface $uri
     * @param array $headers
     * @param StreamInterface $body
     * @param string $protocolVersion
     */
    public function __construct(
        string $method,
        UriInterface $uri,
        array $headers,
        StreamInterface $body,
        string $protocolVersion = 'HTTP/1.1',
    )
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->headers = $headers;
        $this->body = $body;
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * @return string
     */
    public function getRequestTarget(): string
    {
        return $this->uri->getPath();
    }

    /**
     * @param mixed $requestTarget
     * @return $this
     */
    public function withRequestTarget(mixed $requestTarget): static
    {
        return $this->withUri($this->uri->withPath($requestTarget));
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function withMethod(string $method): static
    {
        $request =clone $this;
        $request->method = $method;

        return $request;
    }

    /**
     * @return UriInterface
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @param UriInterface $uri
     * @param bool $preserveHost
     * @return $this
     */
    public function withUri(UriInterface $uri, bool $preserveHost = false): static
    {
        $request = clone $this;
        $request->uri = $uri;

        if ($preserveHost === false) {
            $request =$request->withHeader('Host', $uri->getHost());
        }

        return $request;
    }

    /**
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @param string $version
     * @return $this
     */
    public function withProtocolVersion(string $version): static
    {
        $request = clone $this;
        $request->protocolVersion = $version;

        return $request;
    }

    /**
     * @return array|string[][]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader(string $name): bool
    {
        return isset($this->headers[$name]);
    }

    /**
     * @param string $name
     * @return array|string[]
     */
    public function getHeader(string $name): array
    {
        return $this->headers[$name] ?? [];
    }

    /**
     * @param string $name
     * @return string
     */
    public function getHeaderLine(string $name): string
    {
        return implode(',', $this->headers[$name] ?? []);
    }

    /**
     * @return StreamInterface|array
     */
    public function getArrayBody(): StreamInterface|array
    {
        return $this->body;
    }

    /**
     * @param string $name
     * @param array|string $value
     * @return $this
     */
    public function withHeader(string $name, array|string $value): static
    {
        $request = clone $this;

        $request->headers[$name] = is_array($value) ? $value : [$value];

        return $request;
    }

    /**
     * @param string $name
     * @param array|string $value
     * @return $this
     */
    public function withAddedHeader(string $name, array|string $value): static
    {
        $request = clone $this;
        $request->headers[$name] = array_merge($request->headers[$name] ?? [], $value);

        return $request;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function withoutHeader(string $name): static
    {
        $request = clone $this;
        unset($request->headers[$name]);

        return $request;
    }

    /**
     * @return StreamInterface
     */
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    /**
     * @param StreamInterface $body
     * @return $this
     */
    public function withBody(StreamInterface $body): static
    {
        $request = clone $this;
        $request->body = $body;
        return $request;
    }

    /**
     * @return array
     */
    public function getQueryParams(): array
    {
        return $this->uri->getQueryParams();
    }

    /**
     * @return array
     */
    public function getBodyContents(): array
    {
        return (array) json_decode($this->body->getContents());
    }
}
