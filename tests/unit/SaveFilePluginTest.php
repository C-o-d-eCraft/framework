<?php

namespace Tests\Unit;

use Craft\Console\Plugins\SaveFilePlugin;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\OutputInterface;
use Craft\Components\DIContainer\DIContainer;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

/**
 * Класс для тестирования SaveFilePlugin
 */
class SaveFilePluginTest extends TestCase
{
    /**
     * Тестирует метод init на предмет привязки события
     *
     * @return void
     */
    public function testInitAttachesEvent(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $inputMock = $this->createMock(InputInterface::class);
        $outputMock = $this->createMock(OutputInterface::class);
        $containerMock = $this->createMock(DIContainer::class);

        $containerMock->method('make')
            ->willreturnMap([
                [EventDispatcherInterface::class, $eventDispatcher],
                [InputInterface::class, $inputMock],
                [OutputInterface::class, $outputMock],
            ]);

        $saveFilePlugin = new SaveFilePlugin($containerMock, '/tmp');

        $eventDispatcher->expects($this->once())
            ->method('attach')
            ->with($this->equalTo('after_execute'), $saveFilePlugin);

        $saveFilePlugin->init();
    }

    /**
     * Тестирует метод update на вызов функции file_put_contents с правильными аргументами
     *
     * @return void
     */
    public function testUpdateCallsFilePutContents(): void
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $inputMock = $this->createMock(InputInterface::class);
        $outputMock = $this->createMock(OutputInterface::class);
        $containerMock = $this->createMock(DIContainer::class);

        $containerMock->method('make')
            ->willreturnMap([
                [EventDispatcherInterface::class, $eventDispatcher],
                [InputInterface::class, $inputMock],
                [OutputInterface::class, $outputMock],
            ]);

        $message = "Command output\n";
        $outputMock->method('getMessage')->willReturn($message);

        $filePutContentsMock = $this->getMockBuilder(SaveFilePlugin::class)
            ->onlyMethods(['filePutContents'])
            ->setConstructorArgs([$containerMock, '/tmp'])
            ->getMock();

        $filePutContentsMock->expects($this->once())
            ->method('filePutContents')
            ->with(
                $this->callback(function ($fileName) {
                    return str_contains($fileName, '/tmp/');
                }),
                $this->equalTo("Command output\n"),
                $this->equalTo(FILE_APPEND)
            )
            ->willReturn(true);

        $filePutContentsMock->update();
    }
}
