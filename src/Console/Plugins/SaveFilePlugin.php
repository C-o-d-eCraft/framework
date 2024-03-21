<?php

namespace Craft\Console\Plugins;

use Craft\Components\EventDispatcher\EventMessage;
use Craft\Contracts\ObserverInterface;
use Craft\Components\DIContainer\DIContainer;
use Craft\Components\EventDispatcher\Event;
use Craft\Components\EventDispatcher\EventDispatcher;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\OutputInterface;
use Craft\Contracts\PluginInterface;
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
     * @param EventMessage|null $message
     * @return void
     */
    public function update(EventMessage|null $message = null): void
    {
        $filePath = __DIR__ . 'runtime/console-output';

        if (is_dir($filePath) === false) {
            mkdir($filePath);
        }

        file_put_contents($filePath . '/' . date('Y-m-d H:i:s'), $this->output->getMessage(), FILE_APPEND);
    }
}
