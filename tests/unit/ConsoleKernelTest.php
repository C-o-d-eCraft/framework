<?php

namespace Tests\Unit;

use Craft\Components\DIContainer\DIContainer;
use Craft\Components\ErrorHandler\CliErrorHandler;
use Craft\Components\EventDispatcher\EventDispatcher;
use Craft\Console\Command\AbstractCommand;
use Craft\Console\ConsoleKernel;
use Craft\Console\Input;
use Craft\Console\InputArguments;
use Craft\Console\InputOptions;
use Craft\Console\Output;
use Craft\Contracts\CommandInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\OutputInterface;
use LogicException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ConsoleKernelTest extends TestCase
{
    private function createConsoleKernel(
        ?Input           $input = null,
        ?Output          $output = null,
        ?CliErrorHandler $errorHandler = null,
        ?InputOptions    $inputOptions = null
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
        return new class() extends AbstractCommand {
            public static function getCommandName(): string
            {
                return 'testCommand';
            }

            public static function getDescription(): string
            {
                return 'Test command description';
            }

            public function execute(InputInterface $input, OutputInterface $output): void
            {
            }
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

    public function testHandleIsCorrectForCommandAndTriggersAfterExecute(): void
    {
        $commandMock = $this->createCommandSpy();
        $inputMock = $this->createMock(Input::class);
        $inputOptionsMock = $this->createMock(InputOptions::class);
        $containerMock = $this->createMock(DIContainer::class);
        $outputMock = $this->createMock(Output::class);
        $eventDispatcherMock = $this->createMock(EventDispatcher::class);

        $inputMock->method('getCommandNameSpace')
            ->willReturn('testCommand');
        $inputOptionsMock->method('getCommandMap')
            ->willReturn(['testCommand' => get_class($commandMock)]);
        $containerMock->method('make')
            ->willReturn($commandMock);

        $kernel = new ConsoleKernel(
            $containerMock,
            $inputMock,
            $outputMock,
            $eventDispatcherMock,
            $this->createMock(CliErrorHandler::class),
            $inputOptionsMock
        );

        $result = $kernel->handle();

        $this->assertEquals(0, $result);
    }

    public function testHandleWithErrorsThrowsException(): void
    {
        $commandMock = $this->createCommandSpy();
        $inputMock = $this->createMock(Input::class);
        $inputOptionsMock = $this->createMock(InputOptions::class);
        $containerMock = $this->createMock(DIContainer::class);
        $outputMock = $this->createMock(Output::class);
        $eventDispatcherMock = $this->createMock(EventDispatcher::class);
        $errorHandlerMock = $this->createMock(CliErrorHandler::class);

        $inputMock->method('getCommandNameSpace')
            ->willReturn('testCommand');
        $inputOptionsMock->method('getCommandMap')
            ->willReturn(['testCommand' => get_class($commandMock)]);
        $containerMock->method('make')
            ->will($this->throwException(new RuntimeException('test exception')));
        $errorHandlerMock->method('handle')
            ->willReturn('Handled exception message');

        $kernel = new ConsoleKernel(
            $containerMock,
            $inputMock,
            $outputMock,
            $eventDispatcherMock,
            $errorHandlerMock,
            $inputOptionsMock
        );

        $result = $kernel->handle();

        $this->assertEquals(1, $result);
    }
}
