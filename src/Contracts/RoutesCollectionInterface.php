<?php

namespace Craft\Contracts;

interface RoutesCollectionInterface
{
    /**
     * @return array
     */
    public function getRoutes(): array;

    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $rules
     * @param array $middleware
     * @return void
     */
    public function post(string $path, string|callable $handler, array $rules = [], array $middleware = []): void;

    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $rules
     * @param array $middleware
     * @return void
     */
    public function get(string $path, string|callable $handler, array $rules = [], array $middleware = []): void;

    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $rules
     * @param array $middleware
     * @return void
     */
    public function delete(string $path, string|callable $handler, array $rules = [], array $middleware = []): void;

    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $rules
     * @param array $middleware
     * @return void
     */
    public function put(string $path, string|callable $handler, array $rules = [], array $middleware = []): void;

    /**
     * @param string $path
     * @param string|callable $handler
     * @param array $rules
     * @param array $middleware
     * @return void
     */
    public function patch(string $path, string|callable $handler, array $rules = [], array $middleware = []): void;
}
