<?php

namespace Craft\Components\Logger\Observers;

use Craft\Components\EventDispatcher\EventMessage;
use Craft\Components\Logger\StateProcessor\LogStorageDTO;
use Craft\Contracts\ObserverInterface;

class ObserverAttachExtras implements ObserverInterface
{
    public function __construct(private LogStorageDTO $storage) { }

    /**
     * @param EventMessage|string|null $message
     * @return void
     */
    public function update(mixed $message = null): void
    {
        if ($message === null) {
            return;
        }
        
        if ($this->storage->extras === null) {
            $this->storage->extras = [];
        }

        $this->storage->extras[] = $message->getMessage();
    }
}