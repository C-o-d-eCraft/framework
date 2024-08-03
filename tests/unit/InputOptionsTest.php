<?php

namespace Tests\Unit;

use Craft\Components\DIContainer\DIContainer;
use Craft\Console\ConsoleKernel;
use Craft\Console\Input;
use Craft\Console\InputOptions;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\InputInterface;
use Craft\Components\EventDispatcher\Event;
use Craft\Contracts\ObserverInterface;
use Craft\Console\OptionsConfirm;
use PHPUnit\Framework\TestCase;

class InputOptionsTest extends TestCase
{
    private function createInputOptions(
        ?DIContainer $container = null,
        ?InputInterface $input = null,
        ?EventDispatcherInterface $eventDispatcher = null,
    ): InputOptions {
        return new InputOptions(
            $container ?: $this->createMock(DIContainer::class),
            $input ?: $this->createMock(InputInterface::class),
            $eventDispatcher ?: $this->createMock(EventDispatcherInterface::class)
        );
    }

    public function testRegisterOptionsIsCorrect(): void
    {
        $containerMock = $this->createMock(DIContainer::class);
        $inputMock = $this->createMock(InputInterface::class);
        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);

        $inputMock->method('getOptions')
            ->willReturn(['option1' => 'value1']);

        $containerMock->method('make')
            ->willReturn($this->createMock(OptionsConfirm::class));

        $eventDispatcherMock->expects($this->once())
            ->method('trigger');

        $inputOptions = $this->createInputOptions($containerMock, $inputMock, $eventDispatcherMock);
        $inputOptions->registerOptions();
    }

    public function testNoRegisterOptionsWithEmptyOptions(): void
    {
        $inputMock = $this->createMock(InputInterface::class);
        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);

        $inputMock->method('getOptions')
            ->willReturn([]);

        $eventDispatcherMock->expects($this->never())
            ->method('trigger');

        $inputOptions = $this->createInputOptions(null, $inputMock, $eventDispatcherMock);
        $inputOptions->registerOptions();
    }
}
