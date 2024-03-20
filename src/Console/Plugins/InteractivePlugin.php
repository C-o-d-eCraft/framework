<?php

namespace Framework\Console\Plugins;

use app\DTO\Message;
use app\Observers\ObserverInterface;
use Framework\Components\DIContainer\DIContainer;
use Framework\Components\Event;
use Framework\Components\EventDispatcher\EventDispatcher;
use Framework\Contracts\EventDispatcherInterface;
use Framework\Contracts\InputInterface;
use Framework\Contracts\OutputInterface;
use Framework\Contracts\PluginInterface;
use ReflectionException;

class InteractivePlugin implements PluginInterface, ObserverInterface
{
    /**
     * @var EventDispatcher
     */
    private EventDispatcher $eventDispatcher;

    /**
     * @var InputInterface
     */
    private InputInterface $input;

    /**
     * @var OutputInterface
     */
    private OutputInterface $output;

    /**
     * @var string
     */
    private static string $pluginName = '--interactive';

    /**
     * @var string
     */
    private static string $description = 'Интерактивный режим';

    /**
     * @param DIContainer $container
     * @throws ReflectionException
     */
    public function __construct(private readonly DIContainer $container)
    {
        $this->eventDispatcher = $this->container->make(EventDispatcherInterface::class);
        $this->input = $this->container->make(InputInterface::class);
        $this->output = $this->container->make(OutputInterface::class);
    }

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
     * @param Message|null $message
     * @return void
     */
    public function update(?Message $message = null): void
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
        $this->eventDispatcher->attach(Event::BEFORE_RUN, $this);
    }
}
