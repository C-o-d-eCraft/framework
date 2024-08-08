<?php

namespace Craft\Contracts;

interface DebugTagStorageInterface
{
    /**
     * @return string|null
     */
    public function getTag(): ?string;

    /**
     * @param string $tag
     * @return void
     */
    public function setTag(string $tag): void;
}
