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

class SaveFilePlugin implements PluginInterface, ObserverInterface
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
    private static string $pluginName = '--save-file';

    /**
     * @var string
     */
    private static string $description = 'Сохранить вывод комманды в файл';

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
     * @return void
     */
    public function init(): void
    {
        $this->eventDispatcher->attach(Event::AFTER_EXECUTE, $this);
    }

    /**
     * @param Message|null $message
     * @return void
     */
    public function update(Message|null $message = null): void
    {
        $filePath = PROJECT_ROOT . 'runtime/console-output';

        if (is_dir($filePath) === false) {
            mkdir($filePath);
        }

        file_put_contents($filePath . '/' . date('Y-m-d H:i:s'), $this->output->getMessage(), FILE_APPEND);
    }
}
