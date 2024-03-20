<?php

namespace Framework\Console;

use app\DTO\Message;
use app\Observers\ObserverInterface;
use Framework\Components\Event;
use Framework\Components\EventDispatcher\EventDispatcher;

readonly class OptionsConfirm implements ObserverInterface
{
    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(private EventDispatcher $eventDispatcher) { }

    /**
     * @param Message|null $message
     * @return void
     */
    public function update(?Message $message = null): void
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
                $this->eventDispatcher->trigger(Event::OPTION_CONFIRMED, new Message(['optionsConfirmed' => $plugin]));
            }
        }
    }
}
