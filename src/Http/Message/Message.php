<?php

namespace Craft\Http\Message;

use Craft\Contracts\MessageInterface;
use Craft\Contracts\StreamInterface;

abstract class Message implements MessageInterface
{
    /**
     * @return string
     */
    abstract public function getProtocolVersion(): string;

    /**
     * @param string $version
     * @return $this
     */
    abstract public function withProtocolVersion(string $version): static;

    /**
     * @return array|string[][]
     */
    abstract public function getHeaders(): array;

    /**
     * @param string $name
     * @return bool
     */
    abstract public function hasHeader(string $name): bool;

    /**
     * @param string $name
     * @return array|string[]
     */
    abstract public function getHeader(string $name): array;

    /**
     * @param string $name
     * @return string
     */
    abstract public function getHeaderLine(string $name): string;

    /**
     * @param string $name
     * @param array|string $value
     * @return $this
     */
    abstract public function withHeader(string $name, array|string $value): static;

    /**
     * @param string $name
     * @param array|string $value
     * @return $this
     */
    abstract public function withAddedHeader(string $name, array|string $value): static;

    /**
     * @param string $name
     * @return $this
     */
    abstract public function withoutHeader(string $name): static;

    /**
     * @return StreamInterface
     */
    abstract public function getBody(): StreamInterface;

    /**
     * @param StreamInterface $body
     * @return $this
     */
    abstract public function withBody(StreamInterface $body): static;
}
