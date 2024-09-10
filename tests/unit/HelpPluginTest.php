<?php

namespace Tests\Unit;

use Craft\Console\Plugins\HelpPlugin;
use Craft\Contracts\ConsoleKernelInterface;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\EventMessageInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\OutputInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class HelpPluginTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testUpdateThrowsExceptionForMissingCommandClass()
    {
        $eventDispatcherStub = $this->createStub(EventDispatcherInterface::class);
        $inputStub = $this->createStub(InputInterface::class);
        $outputStub = $this->createStub(OutputInterface::class);
        $consoleKernelStub = $this->createStub(ConsoleKernelInterface::class);

        $plugin = new HelpPlugin($eventDispatcherStub, $inputStub, $outputStub, $consoleKernelStub);
        $messageStub = $this->createStub(EventMessageInterface::class);
        $messageStub->method('getMessage')->willReturn(['commandClass' => null]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Класс команды не был передан');
        $plugin->update($messageStub);
    }

    /**
     * @throws Exception
     */
    public function testUpdateOutputsCommandDescription()
    {
        $eventDispatcherStub = $this->createStub(EventDispatcherInterface::class);
        $inputStub = $this->createStub(InputInterface::class);
        $inputStub->method('getOptions')->willReturn(['--help']);

        $outputStub = $this->createStub(OutputInterface::class);
        $outputStub->expects($this->once())->method('success')->with('Вызов:' . PHP_EOL);


        $consoleKernelStub = $this->createStub(ConsoleKernelInterface::class);
        $consoleKernelStub->expects($this->once())->method('terminate')->with(0);

        $commandClass = new class {
            public static function getFullCommandInfo(): array
            {
                return [
                    'commandName' => 'calculator:calculate-modes',
                    'description' => 'Описание команды',
                    'arguments' => [],
                ];
            }
        };

        $messageStub = $this->createStub(EventMessageInterface::class);
        $messageStub->method('getMessage')->willReturn(['commandClass' => $commandClass]);

        $plugin = new HelpPlugin($eventDispatcherStub, $inputStub, $outputStub, $consoleKernelStub);

        $plugin->update($messageStub);
    }
}
