<?php

namespace Tests\Unit;

use Craft\Components\DIContainer\DIContainer;
use Craft\Components\EventDispatcher\EventMessage;
use Craft\Components\EventDispatcher\Event;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\OutputInterface;
use Craft\Console\Plugins\SaveFilePlugin;
use Codeception\Stub;
use Codeception\Test\Unit;

class SaveFilePluginTest extends Unit
{
    protected $tester;
    private $containerMock;
    private $eventDispatcherMock;
    private $inputMock;
    private $outputMock;
    private $saveFilePlugin;
    private $testLogDir;

    protected function _before()
    {
        $this->eventDispatcherMock = Stub::makeEmpty(EventDispatcherInterface::class);
        $this->inputMock = Stub::makeEmpty(InputInterface::class);
        $this->outputMock = Stub::makeEmpty(OutputInterface::class, [
            'getMessage' => 'Test message'
        ]);

        $this->containerMock = Stub::make(DIContainer::class, [
            'make' => function ($class) {
                if ($class === EventDispatcherInterface::class) {
                    return $this->eventDispatcherMock;
                }
                if ($class === InputInterface::class) {
                    return $this->inputMock;
                }
                if ($class === OutputInterface::class) {
                    return $this->outputMock;
                }
                throw new \LogicException("Не найден: $class");
            }
        ]);

        $this->testLogDir = dirname(__DIR__, 5) . '/runtime/console-output';
        $this->saveFilePlugin = new SaveFilePlugin($this->containerMock);
    }

    public function testInit()
    {
        $this->eventDispatcherMock->expects($this->once())
            ->method('attach')
            ->with(Event::AFTER_EXECUTE, $this->saveFilePlugin);

        $this->saveFilePlugin->init();
    }

    public function testUpdateCreatesLogFile()
    {
        $eventMessageMock = Stub::makeEmpty(EventMessage::class);

        $runtimeDir = $this->testLogDir;
        $logFile = $runtimeDir . '/' . date('Y-m-d H:i:s') . '.log';

        if (!is_dir($runtimeDir)) {
            mkdir($runtimeDir, 0777, true);
        }

        if (file_exists($logFile)) {
            unlink($logFile);
        }

        $this->saveFilePlugin->update($eventMessageMock);

        $this->assertFileExists($logFile, "Лог-файл должен быть создан по пути $logFile");

        $loggedContent = file_get_contents($logFile);

        $this->assertStringContainsString('Test message', $loggedContent, "Лог-файл должен содержать 'Test message'");

        unlink($logFile);
    }
}
