<?php

namespace Tests\Unit;

use Craft\Components\DIContainer\DIContainer;
use Craft\Components\ErrorHandler\CliErrorHandler;
use Craft\Components\EventDispatcher\EventDispatcher;
use Craft\Components\EventDispatcher\EventMessage;
use Craft\Console\ConsoleKernel;
use Craft\Console\InputArguments;
use Craft\Contracts\CommandInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\LoggerInterface;
use Craft\Contracts\ObserverInterface;
use Craft\Contracts\OutputInterface;
use LogicException;
use PHPUnit\Framework\TestCase;

class FakeCommand implements CommandInterface
{
    public static function getCommandName(): string
    {
        return 'test:command';
    }

    public function execute(InputInterface $input, OutputInterface $output): void
    { }

    public static function getDescription(): string
    {
        return 'Fake command for testing';
    }
}

class FakeOptionsConfirm implements ObserverInterface
{
    public function __construct(private EventDispatcher $eventDispatcher) {}

    public function update(EventMessage|null $message = null): void
    {}
}

class ConsoleKernelTest extends TestCase
{
    protected function setUp(): void
    {
        $this->containerMock = $this->createMock(DIContainer::class);
        $this->inputMock = $this->createMock(InputInterface::class);
        $this->outputMock = $this->createMock(OutputInterface::class);
        $this->eventDispatcherMock = $this->createMock(EventDispatcher::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->errorHandlerMock = $this->createMock(CliErrorHandler::class);

        $this->kernel = new ConsoleKernel(
            $this->containerMock,
            $this->inputMock,
            $this->outputMock,
            $this->eventDispatcherMock,
            $this->loggerMock,
            $this->errorHandlerMock,
            ['SomePlugin']
        );
    }

    public function testRegisterCommandNamespaces(): void
    {
        $this->kernel->registerCommandNamespaces([FakeCommand::class]);

        $this->assertArrayHasKey('test:command', $this->kernel->getCommandMap());
    }

    public function testParseCommandArguments(): void
    {
        $commandName = 'command arg1 arg2=default ?arg3';
        $expectedArguments = [
            new InputArguments('arg1'),
            new InputArguments('arg2=default'),
            new InputArguments('?arg3'),
        ];

        $result = $this->kernel->parseCommandArguments($commandName);

        foreach ($result as $index => $arg) {
            $this->assertEquals($expectedArguments[$index]->name, $arg->name);
            $this->assertEquals($expectedArguments[$index]->required, $arg->required);
            $this->assertEquals($expectedArguments[$index]->defaultValue, $arg->defaultValue);
        }
    }

    public function testComparisonArgumentsWithManyArguments(): void
    {
        $commandArguments = [
            new InputArguments('arg1'),
        ];

        $this->inputMock->method('getArguments')->willReturn(['value1', 'value2']);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Избыточное количество аргументов');

        $this->kernel->comparisonArguments($commandArguments);
    }

    public function testComparisonArguments(): void
    {
        $commandArguments = [
            new InputArguments('arg1'),
            new InputArguments('arg2=default')
        ];

        $this->inputMock->method('getArguments')->willReturn(['value1', 'default']);

        $this->inputMock->expects($this->once())
            ->method('setArguments')
            ->with([
                'arg1' => 'value1',
                'arg2' => 'default'
            ]);

        $this->kernel->comparisonArguments($commandArguments);
    }

    public function testRegisteredOptions(): void
    {
        $this->inputMock->method('getOptions')->willReturn(['option1' => 'value1']);

        $fakeOptionsConfirm = new FakeOptionsConfirm($this->eventDispatcherMock);
        $this->containerMock->method('make')->willReturn($fakeOptionsConfirm);

        $this->eventDispatcherMock->expects($this->exactly(2))
            ->method('attach');

        $this->eventDispatcherMock->expects($this->once())
            ->method('trigger')
            ->with('options_confirm', $this->isInstanceOf(EventMessage::class));

        $this->kernel->registeredOptions();
    }

    public function testRegisteredOptionsWithEmptyOptions(): void
    {
        $this->inputMock->method('getOptions')->willReturn([]);

        $fakeOptionsConfirm = new FakeOptionsConfirm($this->eventDispatcherMock);
        $this->containerMock->method('make')->willReturn($fakeOptionsConfirm);

        $this->eventDispatcherMock->expects($this->exactly(2))
            ->method('attach');

        $this->eventDispatcherMock->expects($this->never())
            ->method('trigger');

        $this->kernel->registeredOptions();
    }
}
