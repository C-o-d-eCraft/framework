<?php

namespace Craft\Contracts;

interface EventMessageInterface
{
    /**
     * @return void
     */
    function setMessage(mixed $message): void;

    function getMessage(): mixed;
}
