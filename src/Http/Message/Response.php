<?php

namespace Craft\Http\Message;

use Craft\Contracts\ResponseInterface;
use Craft\Contracts\StreamInterface;

class Response extends Message implements ResponseInterface
{
    /**
     * @var int
     */
    public int $statusCode = 200;

    /**
     * @var array
     */
    public array $headers = [];

    /**
     * @var string
     */
    public string $protocolVersion = 'HTTP/1.1';

    /**
     * @var string|mixed
     */
    public string $reasonPhrase = '';

    /**
     * Stream
     * @var mixed|null
     */
    public mixed $body = null;

    /**
     * @var array|string[]
     */
    private static array $phrases = [
        200 => 'OK',
        301 => 'Moved Permanently',
        400 => 'Bad Request',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error',
        502 => 'Bad Gateway'
    ];

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
        if (!$this->hasHeader($name)) {
            return [];
        }

        return $this->headers[$name];
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return string
     */
    public function getReasonPhrase(): string
    {
        if (!$this->reasonPhrase && isset(static::$phrases[$this->statusCode])) {
            $this->reasonPhrase = static::$phrases[$this->statusCode];
        }

        return $this->reasonPhrase;
    }

    /**
     * @param StreamInterface $body
     * @return void
     */
    public function setBody(StreamInterface $body): void
    {
        $this->body = $body;
    }

    /**
     * @param int $statusCode
     * @return void
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @param string $reasonPhrase
     * @return void
     */
    public function setReasonPhrase(string $reasonPhrase): void
    {
        $this->reasonPhrase = $reasonPhrase;
    }

    /**
     * @param array|string $name
     * @param array|string $value
     * @return void
     */
    public function setHeaders(array|string $name, array|string $value = []): void
    {
        $this->headers = is_array($name) ? $name : [$name => $value];
    }

    /**
     * @param string $name
     * @param array|string $value
     * @return $this
     */
    public function withAddedHeader(string $name, array|string $value): static
    {
        $response = clone $this;
        $response->headers[$name] = $value;

        return $response;
    }

    /**
     * @param int $code
     * @param string $reasonPhrase
     * @return $this
     */
    public function withStatus(int $code, string $reasonPhrase = ''): static
    {
        $this->statusCode = $code;
        $this->reasonPhrase = $reasonPhrase;

        return $this;
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
        $response = clone $this;
        $response->protocolVersion = $version;

        return $response;
    }

    /**
     * @param string $name
     * @param array|string $value
     * @return $this
     */
    public function withHeader(string $name, array|string $value): static
    {
        if ($this->hasHeader($name)) {
            unset($this->headers[$name]);
        }

        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function withoutHeader(string $name): static
    {
        $response = clone $this;
        unset($response->headers[$name]);

        return $response;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getHeaderLine(string $name): string
    {
        $headerValue = $this->headers[$name] ?? '';

        return implode(',', $headerValue);
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
        if ($body === $this->body) {
            return $this;
        }

        $new = clone $this;

        $new->body = $body;

        return $new;
    }

    /**
     * @return void
     */
    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        if (($this->body instanceof StreamInterface) === false) {
            echo 'StatusCode: ' . $this->getStatusCode();
            
            return;
        }

        echo $this->getBody()->getContents();
    }
}
