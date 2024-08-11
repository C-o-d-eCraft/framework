<?php

namespace Craft\Console\Plugins;

use Craft\Components\DIContainer\DIContainer;
use Craft\Console\Events;
use Craft\Components\EventDispatcher\EventMessage;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\ObserverInterface;
use Craft\Contracts\OutputInterface;
use Craft\Contracts\PluginInterface;
use RuntimeException;

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

    /**
     * @param DIContainer $container
     * @throws ReflectionException
     */
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly OutputInterface $output,
        private string $filePath = PROJECT_ROOT . '/runtime/console-output')
    { }

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

        $clearMessage = $this->removeAnsiEscapeSequences($this->output->getMessage());
        $fileName = $this->filePath . '/' . date('Y-m-d H:i:s') . '.log';

        if ($this->filePutContents($fileName, $clearMessage, FILE_APPEND) === false) {
            throw new RuntimeException(sprintf('Ошибка чтения файла "%s"', $fileName));
        }
    }

    protected function filePutContents(string $fileName, string $data, int $flags): bool
    {
        return file_put_contents($fileName, $data, $flags) !== false;
    }

    public function removeAnsiEscapeSequences(string $text): string
    {
        $regex = '/\e\[[0-9;]*m/';
        return preg_replace($regex, '', $text);
    }

    public function outputToFile(): bool
    {
        return in_array('--save-file',$this->options);
    }
}
