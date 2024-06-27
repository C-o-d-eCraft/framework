<?php

namespace Craft\Components\Logger\Observers;

use Craft\Components\EventDispatcher\EventMessage;
use Craft\Components\Logger\StateProcessor\LogStorageDTO;
use Craft\Contracts\ObserverInterface;

class ObserverDetachContext implements ObserverInterface
{
    public function __construct(private LogStorageDTO $storage) { }
    
    /**
     * @param EventMessage|null $message
     * @return void
     */
    public function update(?EventMessage $message = null): void
    {
        if ($message && isset($this->storage->context[$message->getMessage()])) {
            unset($this->storage->context[$message->getMessage()]);
        }
    }
}