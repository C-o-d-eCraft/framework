<?php

namespace Craft\Components\Logger\Observers;

use Craft\Components\Logger\StateProcessor\LogStorageDTO;
use Craft\Contracts\ObserverInterface;

class ObserverDetachContext implements ObserverInterface
{
    public function __construct(private LogStorageDTO $storage) { }

    /**
     * @param mixed|null $message
     * @return void
     */
    public function update(mixed $message = null): void
    {
        if ((is_null($this->storage->context) && is_null($message)) === false) {
            $contextArray = explode(':', $this->storage->context);
            $messageJson = json_encode($message->getMessage(), JSON_UNESCAPED_UNICODE);

            if (($key = array_search($messageJson, $contextArray)) !== false) {
                unset($contextArray[$key]);
                $this->storage->context = implode(':', $contextArray);
            }
        }
    }
}
