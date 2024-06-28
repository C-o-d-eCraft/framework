<?php

namespace Craft\Components\Logger\Observers;

use Craft\Components\EventDispatcher\EventMessage;
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

        // Преобразуем текущее сообщение в строку
        $newContext = $message->getMessage();

        // Проверяем, существует ли уже контекст
        if ($this->storage->context === null) {
            $this->storage->context = $newContext;
        } else {
            // Если существует, добавляем новый контекст с разделителем
            $this->storage->context .= ':' . $newContext;
        }
    }
}