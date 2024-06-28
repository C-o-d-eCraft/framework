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
    public function update(EventMessage|string $message = null): void
    {
        if (is_null($message) === true) {
            return;
        }

        if (is_array($this->storage->extras)) {
            $this->storage->extras[] = json_encode($message->getMessage(), JSON_UNESCAPED_UNICODE);
        }
    }
}