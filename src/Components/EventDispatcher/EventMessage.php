<?php

namespace Craft\Components\EventDispatcher;

use Craft\Contracts\EventMessageInterface;

readonly class EventMessage implements EventMessageInterface
{
    /**
     * @param array $message
     */
    public function __construct(private mixed $message) { }

    /**
     * @return array
     */
    public function getContent(): mixed
    {
        return $this->message;
    }
}
