<?php

namespace Craft\Console\Plugins;

use Craft\Components\EventDispatcher\EventMessage;
use Craft\Console\Events;
use Craft\Console\Exceptions\CommandInterruptedException;
use Craft\Contracts\ConsoleKernelInterface;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\ObserverInterface;
use Craft\Contracts\OutputInterface;
use Craft\Contracts\PluginInterface;
use Craft\Contracts\UnixProcessServiceInterface;
use LogicException;

/**
 * Плагин для перевода выполнения команды в фоновый режим.
 */
class DetachPlugin implements PluginInterface, ObserverInterface
{
    /**
     * @var string Имя плагина.
     */
    private static string $pluginName = '--detach';

    /**
     * @var string Описание плагина.
     */
    private static string $description = 'Перевести выполнение команды в фоновый режим';
    
    public function __construct(
        private readonly EventDispatcherInterface    $eventDispatcher,
        private readonly InputInterface              $input,
        private readonly OutputInterface             $output,
        private readonly UnixProcessServiceInterface $unixProcessService,
        private readonly ConsoleKernelInterface      $consoleKernel,
    ) {
    }

    /**
     * Возвращает имя плагина.
     *
     * @return string Имя плагина.
     */
    public static function getPluginName(): string
    {
        return self::$pluginName;
    }

    /**
     * Возвращает описание плагина.
     *
     * @return string Описание плагина.
     */
    public static function getDescription(): string
    {
        return self::$description;
    }

    /**
     * Инициализирует плагин и привязывает его к событию BEFORE_RUN.
     *
     * @return void
     */
    public function init(): void
    {
        $this->eventDispatcher->attach(Events::BEFORE_RUN->value, $this);
    }

    /**
     * Выполняет основную логику плагина при наступлении события.
     *
     * @param EventMessage|null $message Сообщение события (необязательно).
     * @return void
     */
    public function update(mixed $message = null): void
    {
        if (in_array(self::$pluginName, $this->input->getOptions()) === false) {
            return;
        }

        $this->output->stdout("Перевод выполнения команды в фоновый режим... ");

        $parentPid = $this->unixProcessService->getpid();

        $this->output->stdout("Родительский процесс PID: $parentPid");

        $pid = $this->unixProcessService->fork();

        if ($pid === -1) {
            $error = 'Не удалось создать фоновый процесс';

            $this->output->error($error);
            throw new LogicException($error);
        }

        if ($pid > 0) {
            $this->output->success("Фоновый процесс запущен PID:" . $pid . PHP_EOL);

            $this->output->stdout();
            $this->consoleKernel->terminate(0);
        }

        $this->unixProcessService->setsid();
        $this->unixProcessService->descriptionClose();
        $this->unixProcessService->descriptionOpenDevNull();
    }
}
