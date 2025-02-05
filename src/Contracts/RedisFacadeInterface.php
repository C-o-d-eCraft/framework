<?php

namespace Craft\Contracts;

interface RedisFacadeInterface
{
    /**
     * @param string $key
     * @return mixed
     */
    public function getItem(string $key): mixed;

    /**
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl
     * @return void
     */
    public function setItem(string $key, mixed $value, ?int $ttl = null): void;

    /**
     * @param string $channel
     * @param array $data
     * @return void
     */
    public function publish(string $channel, array $data): void;

    /**
     * @param array $channels
     * @param callable $callback
     * @return void
     */
    public function subscribe(array $channels, callable $callback): void;
}