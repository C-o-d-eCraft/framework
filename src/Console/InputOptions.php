<?php

namespace Craft\Console;

use Craft\Components\DIContainer\DIContainer;
use Craft\Components\EventDispatcher\Event;
use Craft\Components\EventDispatcher\EventMessage;
use Craft\Console\OptionsConfirm;
use Craft\Contracts\CommandInterface;
use Craft\Contracts\ConsoleKernelInterface;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\InputOptionsInterface;
use Craft\Contracts\ObserverInterface;
use LogicException;

class InputOptions implements InputOptionsInterface, ObserverInterface
{
    public function __construct(
        private readonly DIContainer               $container,
        private readonly InputInterface            $input,
        private readonly EventDispatcherInterface  $eventDispatcher,
        private readonly array                     $plugins = [],
        private readonly array                     $options = [],
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

    public function setCommandMap(array $commandMap): void
    {
        $this->commandMap = $commandMap;
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function registerOptions(): void
    {
        $options = $this->input->getOptions();

        if (empty($options) === true) {
            return;
        }

        $optionsConfirm = $this->container->make(OptionsConfirm::class);

        $this->eventDispatcher->attach(Event::OPTIONS_CONFIRM, $optionsConfirm);
        $this->eventDispatcher->attach(Event::OPTION_CONFIRMED, $this->container->make(ConsoleKernelInterface::class));

        $this->eventDispatcher->trigger(Event::OPTIONS_CONFIRM, new EventMessage([
            'options' => $options,
            'commandMap' => $this->commandMap,
            'plugins' => $this->plugins,
        ]));
    }

    /**
     * @param mixed $message
     * @return void
     */
    public function update(mixed $message = null): void
    {
        $optionsConfirmed = $message->getContent()['optionsConfirmed'];

        $optionsConfirmedInstance = new $optionsConfirmed($this->container);

        $optionsConfirmedInstance->init();
    }
}
