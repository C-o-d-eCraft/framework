<?php

namespace Craft\Components\Logger\Observers;

use Craft\Components\EventDispatcher\EventMessage;
use Craft\Components\Logger\StateProcessor\LogStorageDTO;
use Craft\Contracts\ObserverInterface;

class ObserverAttachExtras implements ObserverInterface
{
    public function __construct(private LogStorageDTO $storage) { }

    /**
     * @param EventMessage|null $message The event message containing extra data to be attached.
     * @return void
     */
    public function update(?EventMessage $message = null): void
    {
        if ($message && $message->getMessage()) {
            $this->storage['extras'] = json_encode($message->getMessage(), JSON_UNESCAPED_UNICODE);
        }
    }
}