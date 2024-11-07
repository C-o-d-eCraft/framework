<?php

namespace Craft\Console\Plugins;

use Craft\Components\EventDispatcher\EventMessage;
use Craft\Console\Command\CommandInfoDTO;
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
    
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly InputInterface           $input,
        private readonly OutputInterface          $output,
        private readonly ConsoleKernelInterface   $consoleKernel,
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
        $this->eventDispatcher->attach(Events::BEFORE_RUN->value, $this);
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

            $commandDescription = $commandClass::getFullCommandInfo() ?? 'Для данной команды отсутствует описание';

            $this->writeMessage($commandDescription);

            $this->consoleKernel->terminate(0);
        }
    }

    private function writeMessage(CommandInfoDTO $description): void
    {
        $this->output->success('Вызов:' . PHP_EOL);
        $this->output->text($description->commandName . ' ');

        if ($description->arguments !== []) {
            foreach ($description->arguments as $argument) {
               $this->output->text('[' . $argument['name'] . '] ');
            }
        }

        $this->output->text('[опции]' . PHP_EOL . PHP_EOL);
        $this->output->info('Назначение:' . PHP_EOL);
        $this->output->text($description->description . PHP_EOL . PHP_EOL);

        if ($description->arguments !== []) {
            $this->output->info('Аргументы:' . PHP_EOL);

            foreach ($description->arguments as $argument) {
                $this->output->success($argument['name'] . ' ');
                $this->output->text($argument['info']);
                $this->output->text($argument['required'] === true ? ', обязательный параметр' : ', не обязательный параметр,');
                $this->output->text($argument['defaultValue'] === null ? '' : 'значение по умолчанию: ' . $argument['defaultValue']);
                $this->output->text(PHP_EOL);
            }
        }

        $this->output->stdout();
    }
}
