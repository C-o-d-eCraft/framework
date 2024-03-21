<?php

namespace Craft\Contracts;

interface EventMessageInterface
{
    /**
     * @return mixed
     */
    function getContent(): mixed;
}
