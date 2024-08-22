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
use InvalidArgumentException;

class HelpPlugin implements PluginInterface, ObserverInterface
{
    /**
     * @var string
     */
    private static string $pluginName = '--help';

    /**
     * @var string
     */
    private static string $description = 'Вывести информацию о команде, способе вызова, доступных аргументах и опциях вызова';

    /**
     * @param EventDispatcherInterface $eventDispatcher
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function __construct(
        readonly private EventDispatcherInterface $eventDispatcher,
        readonly private InputInterface $input,
        readonly private OutputInterface $output,
        private readonly ConsoleKernelInterface $consoleKernel,
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
     * @return void
     */
    public function init(): void
    {
        $this->eventDispatcher->attach(Events::BEFORE_RUN, $this);
    }

    /**
     * @param EventMessage|null $message
     * @return void
     */
    public function update(mixed $message = null): void
    {
        $commandClass = $message->getMessage()['commandClass'];

        if ($commandClass === null) {
            throw new InvalidArgumentException('Класс команды не был передан');
        }

        if (in_array(self::$pluginName, $this->input->getOptions()) === true) {

            $commandDescription = $commandClass::getDescription() ?? 'Для данной команды отсутствует описание';

            $this->output->info($commandDescription . PHP_EOL);

            $this->output->stdout();

            $this->consoleKernel->terminate(0);
        }
    }
}
