<?php

namespace Craft\Console;

use Craft\Components\DIContainer\DIContainer;
use Craft\Components\ErrorHandler\CliErrorHandler;
use Craft\Components\EventDispatcher\Event;
use Craft\Components\EventDispatcher\EventDispatcher;
use Craft\Components\EventDispatcher\EventMessage;
use Craft\Contracts\ConsoleKernelInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\InputOptionsInterface;
use Craft\Contracts\LoggerInterface;
use Craft\Contracts\OutputInterface;
use JetBrains\PhpStorm\NoReturn;
use LogicException;
use RuntimeException;
use Throwable;

class ConsoleKernel implements ConsoleKernelInterface
{
    /**
     * @param DIContainer $container
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param EventDispatcher $eventDispatcher
     * @param LoggerInterface $logger
     * @param CliErrorHandler $errorHandler
     * @param InputOptionsInterface $inputOptions
     */
    public function __construct(
        private readonly DIContainer      $container,
        private InputInterface            $input,
        private OutputInterface           $output,
        private readonly EventDispatcher  $eventDispatcher,
        private readonly LoggerInterface  $logger,
        private readonly CliErrorHandler  $errorHandler,
        private InputOptionsInterface     $inputOptions
    ) { }

    /**
     * @param array $commandNameSpaces
     * @return void
     */
    public function registerCommandNamespaces(array $commandNameSpaces): void
    {
        $this->inputOptions->registerCommandNamespaces($commandNameSpaces);
    }

    /**
     * @param int $exitStatus
     * @return void
     */
    #[NoReturn] public function terminate(int $exitStatus): void
    {
        exit($exitStatus);
    }

    /**
     * @return int
     */
    public function handle(): int
    {
        try {
            $calledCommandName = $this->input->getCommandNameSpace();
            $commandMap = $this->inputOptions->getCommandMap();

            $commandClass = $commandMap[$calledCommandName] ?? null;

            if ($commandClass === null) {
                throw new RuntimeException(
                    'Неизвестная команда.' . PHP_EOL . 'Для получения списка команд введите: ' . PHP_EOL . 'list' . PHP_EOL
                );
            }

            $commandArguments = $this->parseCommandArguments($commandClass::getCommandName());

            $this->eventDispatcher->trigger(Event::BEFORE_RUN, new EventMessage(['commandArguments' => $commandArguments]));

            $this->comparisonArguments($commandArguments);

            $this->eventDispatcher->trigger(Event::BEFORE_EXECUTE);

            $this->container->make($commandClass)->execute($this->input, $this->output);

            $this->eventDispatcher->trigger(Event::AFTER_EXECUTE);

            $this->output->stdout($this->output->getMessage());

            return $this->output->getStatusCode();
        } catch (Throwable $e) {
            $message = $this->errorHandler->handle($e);

            $this->output->error($message);

            return 1;
        }
    }

    /**
     * @param string $commandName
     * @return array
     */
    public function parseCommandArguments(string $commandName): array
    {
        $pattern = '/\{([^}]+)}/';

        preg_match_all($pattern, $commandName, $matches);

        $arguments = [];

        if (empty($matches) === true) {
            return [];
        }

        foreach ($matches[1] as $argument) {
            $arguments[] = new InputArguments($argument);
        }

        return $arguments;
    }

    /**
     * @param array $commandArguments
     * @return void
     */
    public function comparisonArguments(array $commandArguments): void
    {
        $inputArguments = $this->input->getArguments();

        if (empty($commandArguments) === true) {
            return;
        }

        $expectedParams = count($commandArguments);
        $actualParams = count($inputArguments);

        if ($actualParams > $expectedParams) {
            throw new LogicException('Избыточное количество аргументов');
        }

        $argumentIndex = 0;
        $enteredArguments = [];

        foreach ($commandArguments as $argument) {
            $paramName = $argument->name;
            $defaultValue = $argument->defaultValue;
            $paramsValue = $inputArguments[$argumentIndex] ?? $defaultValue;

            if ($paramsValue === null && $defaultValue === null) {
                throw new LogicException("\"{$paramName}\" Аргумент обязателен для ввода");
            }

            $enteredArguments[$paramName] = $paramsValue;
            $argumentIndex ++;
        }

        $this->input->setArguments($enteredArguments);
    }
}
