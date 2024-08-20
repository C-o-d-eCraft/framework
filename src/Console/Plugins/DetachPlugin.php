<?php

namespace Craft\Console\Plugins;

use Craft\Components\EventDispatcher\EventMessage;
use Craft\Console\Events;
use Craft\Console\Exceptions\CommandInterruptedException;
use Craft\Contracts\EventDispatcherInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\ObserverInterface;
use Craft\Contracts\OutputInterface;
use Craft\Contracts\PluginInterface;
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

    /**
     * @param EventDispatcherInterface $eventDispatcher Диспетчер событий.
     * @param InputInterface $input Входные данные.
     * @param OutputInterface $output Выходные данные.
     */
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private InputInterface $input,
        private readonly OutputInterface $output,
    ) {}

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
        $this->eventDispatcher->attach(Events::BEFORE_RUN, $this);
    }

    /**
     * Выполняет основную логику плагина при наступлении события.
     *
     * @param EventMessage|null $message Сообщение события (необязательно).
     * @return void
     * @throws CommandInterruptedException Если команда переведена в фоновый режим.
     */
    public function update(mixed $message = null): void
    {
        if (in_array(self::$pluginName, $this->input->getOptions()) === false) {
            return;
        }

        $this->output->info("Перевод выполнения команды в фоновый режим... " . PHP_EOL);

        $parentPid = posix_getpid();

        $this->output->info("Родительский процесс PID: $parentPid" . PHP_EOL);

        $pid = pcntl_fork();

        if ($pid == -1) {
            throw new LogicException('Не удалось создать фоновый процесс');
        }

        if ($pid > 0) {
            $this->output->info("Фоновый процесс запущен с PID: $pid" . PHP_EOL);

            $this->output->info("Родительский процесс убит, PID: $parentPid");

            posix_kill($parentPid, 0);

            throw new CommandInterruptedException('Команда переведена в фоновый режим' . PHP_EOL);
        }

        posix_setsid();
    }
}
