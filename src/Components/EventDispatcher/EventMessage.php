<?php

namespace Craft\Components\EventDispatcher;

use Craft\Contracts\EventMessageInterface;

class EventMessage implements EventMessageInterface
{
    public function __construct(public mixed $message = null) { }

    /**
     * @return array
     */
    public function getMessage(): mixed
    {
        return $this->message;
    }

    /**
     * @return void
     */
    public function setMessage(mixed $message): void
    {
        $this->message = $message;
    }
}
