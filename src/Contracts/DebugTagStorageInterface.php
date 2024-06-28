<?php

namespace Craft\Contracts;

interface DebugTagStorageInterface
{
    public function getTag(): ?string;
    public function setTag(string $tag): void;
}