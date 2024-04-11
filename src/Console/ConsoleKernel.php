<?php

namespace Craft\Console;

use Craft\Components\ErrorHandler\CliErrorHandler;
use Craft\Components\ErrorHandler\MessageEnum;
use Craft\Components\EventDispatcher\EventMessage;
use Craft\Components\Logger\Logger;
use Craft\Contracts\LoggerInterface;
use Craft\Contracts\ObserverInterface;
use Craft\Components\DIContainer\DIContainer;
use Craft\Components\EventDispatcher\Event;
use Craft\Components\EventDispatcher\EventDispatcher;
use Craft\Contracts\CommandInterface;
use Craft\Contracts\ConsoleKernelInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\OutputInterface;
use JetBrains\PhpStorm\NoReturn;
use LogicException;
use ReflectionException;
use RuntimeException;
use Throwable;

class ConsoleKernel implements ConsoleKernelInterface, ObserverInterface
{
    /**
     * @var array
     */
    private array $commandMap = [];

    /**
     * @param DIContainer $container
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param EventDispatcher $eventDispatcher
     * @param array $plugins
     */
    public function __construct(
        private readonly DIContainer      $container,
        private InputInterface            $input,
        private OutputInterface           $output,
        private readonly EventDispatcher  $eventDispatcher,
        private readonly LoggerInterface  $logger,
        private readonly CliErrorHandler  $errorHandler,
        private readonly array            $plugins,

    )
    { }

    /**
     * @param array $commandNameSpaces
     * @return void
     */
    public function registerCommandNamespaces(array $commandNameSpaces): void
    {
        foreach ($commandNameSpaces as $commandClass) {

            if (in_array(CommandInterface::class, class_implements($commandClass), true) === false) {
                throw new LogicException('не удалось прочитать команду');
            }

            $commandName = explode(' ', $commandClass::getCommandName())[0];

            $this->commandMap[$commandName] = $commandClass;
        }
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
     * @return array
     */
    public function getCommandMap(): array
    {
        return $this->commandMap;
    }

    /**
     * @return array
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    /**
     * @return int
     */
    public function handle(): int
    {
        try {
            $this->logger->setContext('Запуск команды');

            $calledCommandName = $this->input->getCommandNameSpace();

            $commandClass = $this->commandMap[$calledCommandName];

            if (empty($commandClass) === true) {
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

            $this->logger->writeLog($e, MessageEnum::INTERNAL_SERVER_ERROR);

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

    public function comparisonArguments(array $commandArguments): void
    {
        $inputArguments = $this->input->getArguments();

        if (empty($commandArguments) === true) {
            return;
        }

        $expectedParams = count($commandArguments);
        $actualParams = count($inputArguments);

        if ($actualParams > $expectedParams) {
            throw new LogicException('Избыточное колличество арргументов');
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

    /**
     * @return void
     * @throws ReflectionException
     */
    public function registeredOptions(): void
    {
        $this->eventDispatcher->attach(Event::OPTIONS_CONFIRM, new OptionsConfirm($this->eventDispatcher));
        $this->eventDispatcher->attach(Event::OPTION_CONFIRMED, $this->container->make(ConsoleKernelInterface::class));

        $options = $this->input->getOptions();

        if ((empty($this->plugins) || empty($options)) === true) {
            return;
        }

        $this->eventDispatcher->trigger(Event::OPTIONS_CONFIRM, new EventMessage([
            'options' => $options,
            'commandMap' => $this->commandMap,
            'plugins' => $this->plugins,
        ]));
    }

    /**
     * @param EventMessage|null $message
     * @return void
     */
    public function update(?EventMessage $message = null): void
    {
        $optionsConfirmed = $message->getContent()['optionsConfirmed'];

        $optionsConfirmedInstance = new $optionsConfirmed($this->container);

        $optionsConfirmedInstance->init();
    }
}
