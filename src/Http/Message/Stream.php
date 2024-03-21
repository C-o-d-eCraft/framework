<?php

namespace Craft\Http\Message;

use Craft\Contracts\StreamInterface;
use InvalidArgumentException;
use RuntimeException;

class Stream implements StreamInterface
{
    /**
     * @var resource
     */
    public mixed $resource;

    /**
     * @var bool
     */
    public bool $closed;

    /**
     * @param mixed $resourceOrString
     * @param bool $closed
     */
    public function __construct(
        mixed $resourceOrString,
        bool $closed = false
    ) {
        $this->closed = $closed;

        if (is_resource($resourceOrString) === true) {
            $this->resource = $resourceOrString;
        }

        if (is_string($resourceOrString) === true) {
            $this->resource = fopen('php://temp', 'r+');
            fwrite($this->resource, $resourceOrString);
            rewind($this->resource);
        }

        if (is_resource($resourceOrString) === false && is_string($resourceOrString) === false) {
            throw new InvalidArgumentException('Указан неверный ресурс или строка');
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getContents();
    }

    /**
     * @return void
     */
    public function close(): void
    {
        if ($this->closed === true) {
            throw new RuntimeException('Поток закрыт');
        }

        fclose($this->resource);

        $this->closed = true;
    }

    /**
     * @return mixed
     */
    public function detach(): mixed
    {
        if ($this->closed === false) {
            $resource = $this->resource;
            $this->resource = null;
            $this->closed = true;
            return $resource;
        }

        return null;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        if ($this->closed === false) {
            $stat = fstat($this->resource);
            return $stat['size'] ?? null;
        }

        return null;
    }

    /**
     * @return int
     */
    public function tell(): int
    {
        if ($this->closed === true) {
            throw new RuntimeException('Поток закрыт');
        }

        $position = ftell($this->resource);

        if ($position === false) {
            throw new RuntimeException('Ошибка получения позиции в потоке');
        }

        return $position;
    }

    /**
     * @return bool
     */
    public function eof(): bool
    {
        if ($this->closed === true) {
            return true;
        }

        return feof($this->resource);
    }

    /**
     * @return bool
     */
    public function isSeekable(): bool
    {
        if ($this->closed === true) {
            return false;
        }

        $metaData = stream_get_meta_data($this->resource);

        return (bool) ($metaData['seekable'] ?? false);
    }

    /**
     * @param int $offset
     * @param int $whence
     * @return int
     */
    public function seek(int $offset, int $whence = SEEK_SET): int
    {
        if ($this->closed === true) {
            throw new RuntimeException('Поток закрыт');
        }

        $result = fseek($this->resource, $offset, $whence);

        if ($result === -1) {
            throw new RuntimeException('Ошибка поиска в потоке');
        }

        return $result;
    }

    /**
     * @return void
     */
    public function rewind(): void
    {
        if ($this->closed === true) {
            throw new RuntimeException('Поток закрыт');
        }

        $this->seek(0, SEEK_SET);
    }

    /**
     * @return bool
     */
    public function isWritable(): bool
    {
        if ($this->closed === true) {
            return false;
        }

        return $this->hasMode('xwca+');
    }

    /**
     * @param string $string
     * @return int
     */
    public function write(string $string): int
    {
        if ($this->closed === true) {
            throw new RuntimeException('Поток закрыт');
        }

        $bytesWritten = fwrite($this->resource, $string);

        if ($bytesWritten === false) {
            throw new RuntimeException('Не удалось записать в поток');
        }

        return $bytesWritten;
    }

    /**
     * @return bool
     */
    public function isReadable(): bool
    {
        if ($this->closed === true) {
            return false;
        }

        return $this->hasMode('r+');
    }

    /**
     * @param int $length
     * @return string
     */
    public function read(int $length): string
    {
        if ($this->closed === true) {
            throw new RuntimeException('Поток закрыт');
        }

        if ($this->isReadable() === false) {
            throw new RuntimeException('Поток не читается');
        }

        $data = fread($this->resource, $length);

        if ($data === false) {
            throw new RuntimeException('Не удалось прочитать из потока');
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getContents(): string
    {
        if ($this->closed === true) {
            throw new RuntimeException('Поток закрыт');
        }

        return stream_get_contents($this->resource);
    }

    /**
     * @param string|null $key
     * @return mixed
     */
    public function getMetadata(string $key = null): mixed
    {
        $metadata = stream_get_meta_data($this->resource);

        if ($key === null) {
            return $metadata;
        }

        return $metadata[$key] ?? null;
    }

    /**
     * Проверяет, содержится ли указанный режим в текущем режиме потока
     *
     * @param string $mode Режим для проверки
     * @return bool
     */
    private function hasMode(string $mode): bool
    {
        $currentMode = $this->getMetadata('mode');

        if ($currentMode !== null && strpbrk($currentMode, $mode) !== false) {
            return true;
        }

        return false;
    }
}
