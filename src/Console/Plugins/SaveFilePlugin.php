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
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @var OutputInterface
     */
    private OutputInterface $output;

    /**
     * @var FileSystemInterface
     */
    private FileSystemInterface $fileSystem;

    private string $filePath;

    /**
     * @var string
     */
    private static string $pluginName = '--save-file';

    /**
     * @var string
     */
    private static string $description = 'Сохранить вывод команды в файл';

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param OutputInterface $output
     * @param FileSystemInterface $fileSystem
     */
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        OutputInterface          $output,
        FileSystemInterface      $fileSystem)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->output = $output;
        $this->fileSystem = $fileSystem;
        $this->filePath = $this->fileSystem->getDirName();
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
        if (is_dir($this->filePath) === false) {
            mkdir($this->filePath);
        }

        $fileName = $this->filePath . '/' . date('Y-m-d H:i:s') . '.log';

        $this->fileSystem->put($fileName, $this->output->getMessage(), FILE_APPEND);
    }

}
