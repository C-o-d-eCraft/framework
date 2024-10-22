<?php

namespace Craft\Console\Plugins;

use Craft\Components\EventDispatcher\EventMessage;
use Craft\Console\Events;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\FileSystemInterface;
use Craft\Contracts\ObserverInterface;
use Craft\Contracts\OutputInterface;
use Craft\Contracts\PluginInterface;

class SaveFilePlugin implements PluginInterface, ObserverInterface
{
    /**
     * @var string
     */
    private static string $pluginName = '--save-file';

    /**
     * @var string
     */
    private static string $description = 'Сохранить вывод команды в файл';
    
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly OutputInterface          $output,
        private readonly FileSystemInterface      $fileSystem
    ) {
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
        $this->eventDispatcher->attach(Events::AFTER_EXECUTE, $this);
    }

    /**
     * @param EventMessage|null $message
     * @return void
     */
    public function update(mixed $message = null): void
    {
        $path = $this->fileSystem->getAlias('runtime_path');

        if (is_dir($path) === false) {
            mkdir($path);
        }

        $fileName = $path . '/' . date('Y-m-d H:i:s');

        $this->fileSystem->put($fileName, $this->output->getMessage(), FILE_APPEND);
    }
}
