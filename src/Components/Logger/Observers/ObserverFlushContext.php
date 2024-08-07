<?php

namespace Craft\Components\Logger\Observers;

use Craft\Components\Logger\StateProcessor\LogStorageDTO;
use Craft\Contracts\ObserverInterface;

class ObserverFlushContext implements ObserverInterface
{
    public function __construct(private LogStorageDTO $storage) { }

    /**
     * @param mixed|null $message
     * @return void
     */
    public function update(mixed $message = null): void
    {
        $this->storage->context = null;
    }
}
