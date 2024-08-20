<?php

namespace Craft\Console\Plugins;

use Craft\Components\EventDispatcher\EventMessage;
use Craft\Console\Events;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\ObserverInterface;
use Craft\Contracts\OutputInterface;
use Craft\Contracts\PluginInterface;

class InteractivePlugin implements PluginInterface, ObserverInterface
{
    /**
     * @var string
     */
    private static string $pluginName = '--interactive';

    /**
     * @var string
     */
    private static string $description = 'Интерактивный режим';

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly InputInterface $input,
        private readonly OutputInterface $output
    ) { }

    /**
     * @return string
     */
    public static function getPluginName(): string
    {
        return self::$pluginName;
    }

    /**
     * @return string
     */
    public static function getDescription(): string
    {
        return self::$description;
    }

    /**
     * @param EventMessage|null $message
     * @return void
     */
    public function update(mixed $message = null): void
    {
        $commandArguments = $message->getContent()['commandArguments'];

        $arguments = [];

        foreach ($commandArguments as $argument) {
            $message = "Введите аргумент " . $argument->name;

            if (empty($argument->defaultValue) === false) {
                $message .= " [$argument->defaultValue]";
            }

            $this->output->stdout($message . PHP_EOL);

            if ($argument->required === false) {
                $inputValue = trim(fgets(STDIN));
                $arguments[] = $inputValue !== '' ? $inputValue : $argument->defaultValue;

                continue;
            }

            do {
                $inputValue = trim(fgets(STDIN));
                $this->output->stdout(($inputValue === '' ? "$argument->name - Обязательный аргумент. Пожалуйста, введите значение." . PHP_EOL : ''));
            } while ($inputValue === '');

            $arguments[] = $inputValue;
        }

        $this->input->setArguments($arguments);
    }

    /**
     * @return void
     */
    public function init(): void
    {
        $this->eventDispatcher->attach(Events::BEFORE_RUN, $this);
    }
}
