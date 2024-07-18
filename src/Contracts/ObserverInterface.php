<?php

namespace Craft\Contracts;

interface ObserverInterface
{
    public function update(mixed $message = null): void;
}
