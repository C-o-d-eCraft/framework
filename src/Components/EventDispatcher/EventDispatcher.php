<?php

namespace Craft\Components\EventDispatcher;

use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\ObserverInterface;

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
     * @param EventMessage|null $message
     * @return void
     */
    public function trigger(string $event, EventMessage $message = null): void
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
