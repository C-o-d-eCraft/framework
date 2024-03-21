<?php

namespace Tests\Unit;

use Craft\Contracts\ObserverInterface;
use Craft\Components\EventDispatcher\EventDispatcher;
use PHPUnit\Framework\MockObject\Exception;
use Codeception\{Test\Unit};

class EventDispatcherTest extends Unit
{
    /**
     * @throws Exception
     */
    public function testAttach(): void
    {
        $observer = $this->createMock(ObserverInterface::class);

        $dispatcher = new class extends EventDispatcher {
            public function getObservers(): array
            {
                return $this->observers;
            }
        };

        $dispatcher->attach('test_event', $observer);

        $this->assertArrayHasKey('test_event', $dispatcher->getObservers());
    }

    /**
     * @throws Exception
     */
    public function testGetNotAttached(): void
    {
        $observer = $this->createMock(ObserverInterface::class);

        $dispatcher = new class extends EventDispatcher {
            public function getObservers(): array
            {
                return $this->observers;
            }
        };

        $dispatcher->attach('test_event', $observer);

        $this->assertArrayNotHasKey('event', $dispatcher->getObservers());
    }

    /**
     * @throws Exception
     */
    public function testDetach(): void
    {
        $observer = $this->createMock(ObserverInterface::class);
        $observer->expects($this->never())->method('update');

        $dispatcher = new class extends EventDispatcher {
            public function getObservers(): array
            {
                return $this->observers;
            }
        };

        $dispatcher->attach('test_event', $observer);
        $dispatcher->detach('test_event');

        $dispatcher->trigger('test_event');

        $this->assertArrayNotHasKey('test_event', $dispatcher->getObservers());
    }

    /**
     * @throws Exception
     */
    public function testTrigger(): void
    {
        $observer = $this->createMock(ObserverInterface::class);
        $observer->expects($this->once())->method('update');

        $dispatcher = new EventDispatcher();

        $dispatcher->attach('test_event', $observer);

        $dispatcher->trigger('test_event');
    }

    /**
     * @throws Exception
     */
    public function testTriggerMultiple(): void
    {
        $observer = $this->createMock(ObserverInterface::class);
        $observer->expects($this->exactly(2))->method('update');

        $dispatcher = new EventDispatcher();

        $dispatcher->attach('test_event', $observer);

        $dispatcher->trigger('test_event');
        $dispatcher->trigger('test_event');
    }
}
