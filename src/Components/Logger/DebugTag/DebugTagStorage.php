<?php

namespace Craft\Components\Logger\DebugTag;

use Craft\Contracts\DebugTagStorageInterface;

class DebugTagStorage implements DebugTagStorageInterface
{
    /**
     * @var string|null
     */
    private $tag = null;

    /**
     * @return string|null
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * @param string $tag
     * @return void
     */
    public function setTag(string $tag): void
    {
        $this->tag = $tag;
    }
}