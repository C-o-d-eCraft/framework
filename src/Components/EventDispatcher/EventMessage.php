<?php

namespace Craft\Components\EventDispatcher;

use Craft\Contracts\EventMessageInterface;

readonly class EventMessage implements EventMessageInterface
{
    /**
     * @param array $items
     */
    public function __construct(private mixed $items) { }

    /**
     * @return array
     */
    public function getContent(): mixed
    {
        return $this->items;
    }
}
