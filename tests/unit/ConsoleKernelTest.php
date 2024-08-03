<?php

namespace Tests\Unit;

use Craft\Components\DIContainer\DIContainer;
use Craft\Components\ErrorHandler\CliErrorHandler;
use Craft\Components\EventDispatcher\EventDispatcher;
use Craft\Console\ConsoleKernel;
use Craft\Contracts\CommandInterface;
use Craft\Console\Input;
use Craft\Console\InputOptions;
use Craft\Contracts\InputInterface;
use Craft\Contracts\InputOptionsInterface;
use Craft\Contracts\OutputInterface;
use Craft\Console\InputArguments;
use Craft\Console\Output;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use LogicException;

class ConsoleKernelTest extends TestCase
{
    private function createConsoleKernel(
        ?Input $input = null,
        ?Output $output = null,
        ?CliErrorHandler $errorHandler = null,
        ?InputOptions $inputOptions = null
    ): ConsoleKernel 
    {
        return new ConsoleKernel(
            $this->createMock(DIContainer::class),
            $input ?: $this->createMock(Input::class),
            $output ?: $this->createMock(Output::class),
            $this->createMock(EventDispatcher::class),
            $errorHandler ?: $this->createMock(CliErrorHandler::class),
            $inputOptions ?: $this->createMock(InputOptions::class)
        );
    }

    private function createCommandSpy(): CommandInterface
    {
        return new class() implements CommandInterface
        {
            public static function getCommandName(): string
            {
                return 'testCommand';
            }

            public static function getDescription(): string
            {
                return 'Test command description';
            }

            public function execute(InputInterface $input, OutputInterface $output): void
            { }
        };
    }

    public function testHandleInputFromConsoleWithUnknownCommand(): void
    {
        $input = $this->createMock(Input::class);
        $input->method('getCommandNameSpace')
            ->willReturn('unknownCommand');

        $inputOptions = $this->createMock(InputOptions::class);
        $inputOptions->method('getCommandMap')
            ->willReturn([]);

        $errorHandler = $this->createMock(CliErrorHandler::class);
        $errorHandler->method('handle')
            ->will($this->returnCallback(function ($exception) {
                return $exception->getMessage();
            }));

        $output = $this->createMock(Output::class);
        $output->expects($this->once())
            ->method('error')
            ->with('Неизвестная команда.' . PHP_EOL . 'Для получения списка команд введите: ' . PHP_EOL . 'list' . PHP_EOL);

        $consoleKernel = $this->createConsoleKernel($input, $output, $errorHandler, $inputOptions);

        $result = $consoleKernel->handle();
    }


    public function testParseArgumentsWithExcessArgumentsThrowLogicException(): void
    {
        $input = $this->createMock(Input::class);
        $input->method('getArguments')->willReturn(['arg1', 'arg2']);
        
        $consoleKernel = $this->createConsoleKernel($input);

        $commandArguments = [
            new InputArguments('arg1'),
        ];

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Избыточное количество аргументов');

        $consoleKernel->comparisonArguments($commandArguments);
    }

    public function testParseArgumentsWithRequiredArgumentMissingThrowLogicException(): void
    {
        $input = $this->createMock(Input::class);
        $input->method('getArguments')->willReturn([]);

        $consoleKernel = $this->createConsoleKernel($input);

        $commandArguments = [
            new InputArguments('arg1'),
        ];

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('"arg1" Аргумент обязателен для ввода');

        $consoleKernel->comparisonArguments($commandArguments);
    }

    public function testRegisterCommandNamespaceIsCorrectly(): void
    {
        $commandStub = $this->createCommandSpy();

        $inputOptionsMock = $this->createMock(InputOptions::class);

        $inputOptionsMock->expects($this->once())
            ->method('setCommandMap')
            ->with($this->callback(function (array $commandMap) {
                return isset($commandMap['testCommand']);
            }));

        $consoleKernelMock = $this->createConsoleKernel(null, null, null, $inputOptionsMock);
        $consoleKernelMock->registerCommandNamespaces([get_class($commandStub)]);
    }

    public function testInabilityReadCommandThrowsLogicException(): void
    {
        $invalidCommandClass = \stdClass::class;

        $consoleKernel = $this->createConsoleKernel();

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage("Класс {$invalidCommandClass} команды не соответствует интерфейсу " . CommandInterface::class);


        $consoleKernel->registerCommandNamespaces([$invalidCommandClass]);
    }
}
