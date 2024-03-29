<?php

namespace Craft\Contracts;

interface ContainerInterface
{
    /**
     * @param array $config
     * @return self
     */
    public static function createContainer(array $config = []): self;

    /**
     * @param string $contract
     * @return object
     */
    public function make(string $contract): object;

    /**
     * @param string $contract
     * @return void
     */
    public function singleton(string $contract): void;

    /**
     * @param callable|object $handler
     * @param string $method
     * @return mixed
     */
    public function call(callable|object|string $handler, string $method, array $args = []): mixed;

    /**
     * @param string $contract
     * @return bool
     */
    public function has(string $contract): bool;
}
