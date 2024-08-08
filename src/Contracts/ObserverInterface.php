<?php

namespace Craft\Contracts;

interface ObserverInterface
{
    /**
     * @param mixed|null $message
     * @return void
     */
    public function update(mixed $message = null): void;
}
