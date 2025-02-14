<?php

namespace Craft\Console;

use Craft\Components\DIContainer\DIContainer;
use Craft\Components\ErrorHandler\CliErrorHandler;
use Craft\Components\EventDispatcher\EventMessage;
use Craft\Console\Command\ListCommand;
use Craft\Console\Exceptions\CommandInterruptedException;
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
    public function __construct(
        private readonly DIContainer              $container,
        private InputInterface                    $input,
        private OutputInterface                   $output,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly CliErrorHandler          $errorHandler,
        private readonly InputOptionsInterface    $inputOptions
    ) {
    }

    /**
     * @param array $commandNameSpaces
     * @return void
     */
    public function registerCommandNamespaces(array $commandNameSpaces): void
    {
        $commandMap = [];

        $commandNameSpaces[] = ListCommand::class;

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

            if (empty($calledCommandName) === true) {
                $calledCommandName = ListCommand::getCommandName();
            }

            $commandClass = $commandMap[$calledCommandName] ?? null;

            if ($commandClass === null) {
                throw new RuntimeException(
                    'Неизвестная команда.' . PHP_EOL . 'Для получения списка команд введите: ' . PHP_EOL . 'list' . PHP_EOL
                );
            }

            $this->initializePlugins();

            $commandArguments = $this->input->parseCommandArguments($commandClass::getCommandName());

            $this->eventDispatcher->trigger(
                Events::BEFORE_RUN,
                new EventMessage([
                    'commandArguments' => $commandArguments,
                    'commandClass' => $commandClass,
                ]));

            $this->input->comparisonArguments($commandArguments);

            $this->eventDispatcher->trigger(Events::BEFORE_EXECUTE);

            $this->container->make($commandClass)->execute($this->input, $this->output);

            $this->output->stdout($this->output->getMessage());

            $this->eventDispatcher->trigger(Events::AFTER_EXECUTE);

            return $this->output->getStatusCode();

        } catch (Throwable $e) {
            $message = $this->errorHandler->handle($e);

            $this->output->error($message);

            return 1;
        }
    }

    /**
     * Инициализирует плагины, которые были вызваны в команде
     * @throws \ReflectionException
     */
    private function initializePlugins(): void
    {
        $plugins = $this->inputOptions->getPlugins();

        if (empty($plugins) === true) {
            return;
        }

        foreach ($plugins as $plugin) {
            $plugin = $this->container->make($plugin);

            if (in_array($plugin->getPluginName(), $this->input->getOptions())) {
                $plugin->init();
            }
        }
    }
}
