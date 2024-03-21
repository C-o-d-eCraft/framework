<?php

namespace Craft\Contracts;

use Craft\Components\EventDispatcher\EventMessage;

interface ObserverInterface
{
    public function update(EventMessage|null $message = null): void;
}
