<?php

namespace Craft\Contracts;

interface FileSystemInterface
{
    public function getDirName(): string;

    public function put(string $path, string $content, int $flags = 0): void;
}