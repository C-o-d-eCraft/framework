<?php

namespace Craft\Contracts;

interface FileSystemInterface
{
    public function __construct(array $config);

    public function getAlias(string $name): string;

    public function put(string $path, string $content, int $flags = 0): void;
}