<?php

namespace Tests\Unit;

use Craft\Console\Exceptions\CommandInterruptedException;
use Craft\Console\Plugins\HelpPlugin;
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

        $plugin = new HelpPlugin($eventDispatcherStub, $inputStub, $outputStub);
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
        $outputStub->expects($this->once())->method('info')->with('Test Command Description' . PHP_EOL);

        $commandClass = new class {
            public static function getDescription(): string
            {
                return 'Test Command Description';
            }
        };

        $messageStub = $this->createStub(EventMessageInterface::class);
        $messageStub->method('getMessage')->willReturn(['commandClass' => $commandClass]);
        $plugin = new HelpPlugin($eventDispatcherStub, $inputStub, $outputStub);

        $this->expectException(CommandInterruptedException::class);
        $this->expectExceptionMessage('Опция не подразумевает выполнения команды, только вывод информации о ней');
        $plugin->update($messageStub);
    }
}
