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
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcherMock;

    /**
     * @var OutputInterface
     */
    private OutputInterface $outputMock;

    /**
     * @var DIContainer
     */
    private DIContainer $containerMock;

    /**
     * @var SaveFilePlugin
     */
    private SaveFilePlugin $saveFilePlugin;

    /**
     * Устанавливает начальные условия для тестов
     *
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $inputMock = $this->createMock(InputInterface::class);
        $this->outputMock = $this->createMock(OutputInterface::class);
        $this->containerMock = $this->createMock(DIContainer::class);

        $this->containerMock->method('make')
            ->willreturnMap([
                [EventDispatcherInterface::class, $this->eventDispatcherMock],
                [InputInterface::class, $inputMock],
                [OutputInterface::class, $this->outputMock],
            ]);

        $this->saveFilePlugin = new SaveFilePlugin($this->containerMock, '/tmp');
    }

    /**
     * Тестирует метод init на предмет привязки события
     *
     * @return void
     */
    public function testInitAttachesEvent(): void
    {
        $this->eventDispatcherMock->expects($this->once())
            ->method('attach')
            ->with($this->equalTo('after_execute'), $this->saveFilePlugin);

        $this->saveFilePlugin->init();
    }

    /**
     * Тестирует метод update на вызов функции file_put_contents с правильными аргументами
     *
     * @return void
     */
    public function testUpdateCallsFilePutContents(): void
    {
        $message = "Command output\n";
        $this->outputMock->method('getMessage')->willReturn($message);

        $filePutContentsMock = $this->getMockBuilder(SaveFilePlugin::class)
            ->onlyMethods(['filePutContents'])
            ->setConstructorArgs([$this->containerMock, '/tmp'])
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

    /**
     * Тестирует метод removeAnsiEscapeSequences на удаление ANSI последовательностей
     *
     * @return void
     */
    public function testRemoveAnsiEscapeSequences(): void
    {
        $text = "\e[32mSuccess\e[0m";
        $result = $this->saveFilePlugin->removeAnsiEscapeSequences($text);
        $this->assertEquals("Success", $result);
    }
}
