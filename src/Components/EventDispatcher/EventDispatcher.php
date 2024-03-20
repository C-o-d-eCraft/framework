<?php

namespace Framework\Components\EventDispatcher;

use Framework\Contracts\EventDispatcherInterface;
use app\DTO\Message;
use app\Observers\ObserverInterface;

class EventDispatcher implements EventDispatcherInterface
{
    /**
     * @var array
     */
    protected array $observers = [];

    /**
     * @param array $observers
     */
    public function __construct(array $observers = []) {
        foreach ($observers as $id => $observer) {
            $this->observers[array_shift($observer)] = $observer;
        }
    }

    /**
     * @param string $event
     * @param ObserverInterface $observer
     * @return void
     */
    public function attach(string $event, ObserverInterface $observer): void
    {
        if (isset($this->observers[$event]) === false) {
            $this->observers[$event] = [];
        }

        $this->observers[$event][] = $observer;
    }

    /**
     * @param string $event
     * @return void
     */
    public function detach(string $event): void
    {
        unset($this->observers[$event]);
    }

    /**
     * @param string $event
     * @param Message|null $message
     * @return void
     */
    public function trigger(string $event, Message $message = null): void
    {
        if (isset($this->observers[$event]) === false) {
            return;
        }

        foreach ($this->observers[$event] as $observer) {
            $observer->update($message);
        }
    }

    /**
     * @return array
     */
    public function getObservers(): array
    {
        return $this->observers;
    }
}
