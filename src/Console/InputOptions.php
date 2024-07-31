<?php

namespace Craft\Console;

use Craft\Components\DIContainer\DIContainer;
use Craft\Components\EventDispatcher\Event;
use Craft\Components\EventDispatcher\EventMessage;
use Craft\Contracts\CommandInterface;
use Craft\Contracts\ConsoleKernelInterface;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\InputOptionsInterface;
use Craft\Contracts\OutputInterface;

class InputOptions implements InputOptionsInterface
{
    public function __construct(
        private readonly DIContainer               $container,
        private readonly InputInterface            $input,
        private readonly OutputInterface           $output,
        private readonly EventDispatcherInterface  $eventDispatcher,
        private readonly array                     $plugins,
        private array                              $options = [],
        private array                              $commandMap = [],
    ) { }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getPlugins(): array
    {
        return $this->plugins;
    }

    public function getCommandMap(): array
    {
        return $this->commandMap;
    }

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
     * @return void
     * @throws ReflectionException
     */
    public function registerOptions(): void
    {
        $this->eventDispatcher->attach(Event::OPTIONS_CONFIRM, new OptionsConfirm($this->eventDispatcher));
        $this->eventDispatcher->attach(Event::OPTION_CONFIRMED, $this->container->make(ConsoleKernelInterface::class));

        $options = $this->input->getOptions();

        if (empty($options) === true) {
            return;
        }

        $this->eventDispatcher->trigger(Event::OPTIONS_CONFIRM, new EventMessage([
            'options' => $options,
            'commandMap' => $this->commandMap,
            'plugins' => $this->plugins,
        ]));
        
        dd($this->commandMap, $this->options);
    }

    /**
     * @param EventMessage|null $message
     * @return void
     */
    public function update(mixed $message = null): void
    {
        $optionsConfirmed = $message->getContent()['optionsConfirmed'];

        $optionsConfirmedInstance = new $optionsConfirmed($this->container);

        $optionsConfirmedInstance->init();
    }
}
