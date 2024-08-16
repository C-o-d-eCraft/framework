<?php

namespace Craft\Console;

use Craft\Components\DIContainer\DIContainer;
use Craft\Components\ErrorHandler\CliErrorHandler;
use Craft\Components\EventDispatcher\EventMessage;
use Craft\Console\Command\ListCommand;
use Craft\Contracts\CommandInterface;
use Craft\Contracts\ConsoleKernelInterface;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\InputOptionsInterface;
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
     * @param EventDispatcherInterface $eventDispatcher
     * @param CliErrorHandler $errorHandler
     * @param InputOptionsInterface $inputOptions
     */
    public function __construct(
        private readonly DIContainer              $container,
        private InputInterface                    $input,
        private OutputInterface                   $output,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CliErrorHandler          $errorHandler,
        private InputOptionsInterface             $inputOptions
    )
    {
    }

    /**
     * @param array $commandNameSpaces
     * @return void
     */
    public function registerCommandNamespaces(array $commandNameSpaces): void
    {
        $commandMap = [];

        foreach ($commandNameSpaces as $commandClass) {
            if (in_array(CommandInterface::class, class_implements($commandClass), true) === false) {
                throw new LogicException("Класс {$commandClass} команды не соответствует интерфейсу " . CommandInterface::class);
            }

            $commandName = explode(' ', $commandClass::getCommandName())[0];

            $commandMap[$commandName] = $commandClass;
        }

        $this->inputOptions->setCommandMap($commandMap);
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
            $plugins = $this->inputOptions->getPlugins();

            if (empty($calledCommandName) === true) {
                $calledCommandName = ListCommand::getCommandName();
            }

            foreach ($plugins as $plugin) {
                $plugin = $this->container->make($plugin);
                $plugin->init();
            }

            $commandClass = $commandMap[$calledCommandName] ?? null;

            if ($commandClass === null) {
                throw new RuntimeException(
                    'Неизвестная команда.' . PHP_EOL . 'Для получения списка команд введите: ' . PHP_EOL . 'list' . PHP_EOL
                );
            }

            $commandArguments = $this->parseCommandArguments($commandClass::getCommandName());

            $this->eventDispatcher->trigger(Events::BEFORE_RUN, new EventMessage(['commandArguments' => $commandArguments]));

            $this->comparisonArguments($commandArguments);

            $this->eventDispatcher->trigger(Events::BEFORE_EXECUTE);

            $this->container->make($commandClass)->execute($this->input, $this->output);

            if ($this->input->outputToFile() === true) {
                $this->eventDispatcher->trigger(Events::AFTER_EXECUTE);
            }

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
            $argumentIndex++;
        }

        $this->input->setArguments($enteredArguments);
    }
}
