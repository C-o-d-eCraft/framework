<?php

namespace Craft\Contracts;

interface EventMessageInterface
{
    /**
     * @return mixed
     */
    function getMessage(): mixed;

    /**
     * @return void
     */
    function setMessage(mixed $message): void;
}
