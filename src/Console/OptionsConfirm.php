<?php

namespace Craft\Console;

use Craft\Components\EventDispatcher\EventMessage;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\ObserverInterface;
use Craft\Components\EventDispatcher\Event;

readonly class OptionsConfirm implements ObserverInterface
{
    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(private EventDispatcherInterface $eventDispatcher) { }

    /**
     * @param EventMessage|null $message
     *
     * @return void
     */
    public function update(?EventMessage $message = null): void
    {
        [
            'commandMap' => $commandMap,
            'plugins' => $plugins,
            'options' => $options
        ] = $message->getContent();

        foreach ($commandMap as $command) {
            $commandName = $command::getCommandName();

            preg_match_all('/\s--\S+/', $commandName, $matches);

            $options = array_merge($matches[0], $options);
        }

        foreach ($plugins as $plugin) {
            $pluginName = $plugin::getPluginName();

            if (in_array($pluginName, $options, true) === true) {
                $this->eventDispatcher->trigger(Event::OPTION_CONFIRMED, new EventMessage(['optionsConfirmed' => $plugin]));
            }
        }
    }
}
