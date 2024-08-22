<?php

namespace Tests\Unit;

use Craft\Console\Exceptions\CommandInterruptedException;
use Craft\Console\Plugins\DetachPlugin;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\OutputInterface;
use Craft\Contracts\UnixProcessServiceInterface;
use PHPUnit\Framework\TestCase;
use LogicException;

class DetachPluginTest extends TestCase
{
    /**
     * Тест успешного перевода команды в фоновый режим.
     */
    public function testUpdateSuccessfullyDetachesProcess(): void
    {
        $eventDispatcherStub = $this->createStub(EventDispatcherInterface::class);
        $inputStub = $this->createStub(InputInterface::class);
        $outputStub = $this->createStub(OutputInterface::class);
        $unixProcessService = $this->createStub(UnixProcessServiceInterface::class);

        $inputStub->method('getOptions')->willReturn(['--detach']);
        $plugin = new DetachPlugin($eventDispatcherStub, $inputStub, $outputStub, $unixProcessService);
        $unixProcessService->method('fork')->willReturn(2);

        $this->expectException(CommandInterruptedException::class);
        $plugin->update();
    }

    /**
     * Тест обработки ошибки при форке процесса.
     */
    public function testUpdateThrowsExceptionOnForkFailure(): void
    {
        $eventDispatcherStub = $this->createStub(EventDispatcherInterface::class);
        $inputStub = $this->createStub(InputInterface::class);
        $outputStub = $this->createStub(OutputInterface::class);
        $unixProcessService = $this->createStub(UnixProcessServiceInterface::class);

        $inputStub->method('getOptions')->willReturn(['--detach']);
        $plugin = new DetachPlugin($eventDispatcherStub, $inputStub, $outputStub, $unixProcessService);
        $unixProcessService->method('fork')->willReturn(-1);

        $this->expectException(LogicException::class);
        $plugin->update();
    }
}
