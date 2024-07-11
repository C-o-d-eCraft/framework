<?php

namespace Tests\Unit;

use Craft\Components\DIContainer\DIContainer;
use Craft\Components\ErrorHandler\CliErrorHandler;
use Craft\Components\EventDispatcher\EventDispatcher;
use Craft\Console\ConsoleKernel;
use Craft\Console\InputArguments;
use Craft\Contracts\CommandInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\LoggerInterface;
use Craft\Contracts\OutputInterface;
use LogicException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Throwable;

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
            []
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

    public function testComparisonArgumentsExcess(): void
    {
        $commandArguments = [
            new InputArguments('arg1'),
        ];

        $this->inputMock->method('getArguments')->willReturn(['value1', 'value2']);

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Избыточное количество аргументов');

        $this->kernel->comparisonArguments($commandArguments);
    }
}
