<?php

namespace Craft\Components\Logger\Observers;

use Craft\Components\Logger\StateProcessor\LogStorageDTO;
use Craft\Contracts\ObserverInterface;

class ObserverAttachContext implements ObserverInterface
{
    public function __construct(private LogStorageDTO $storage) { }

    /**
     * @param mixed $message
     * @return void
     */
    public function update(mixed $message = null): void
    {
        if (is_null($message)) {
            return;
        }

        $newContext = $message->getMessage();

        if ($this->storage->context === null) {
            $this->storage->context = $newContext;

            return;
        }

        $this->storage->context .= ':' . $newContext;
    }
}
