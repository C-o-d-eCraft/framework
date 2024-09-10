<?php

namespace Craft\Console\Command;

use Craft\Contracts\InputInterface;
use Craft\Contracts\InputOptionsInterface;
use Craft\Contracts\OutputInterface;

class ListCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected static string $commandName = 'list';

    /**
     * @var string
     */
    public static string $description = 'Выводит список всех доступных команд и опций';

    /**
     * @param InputOptionsInterface $inputOptions
     */
    public function __construct(private readonly InputOptionsInterface $inputOptions) { }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->primary(" C-o-d-eCraft Фреймворк" . PHP_EOL . PHP_EOL);
        $output->warning(
        " Фреймворк создан разработчиками C-o-d-eCraft." . PHP_EOL .
            " Является платформой для изучения базового поведения приложения созданного на PHP." . PHP_EOL .
            " Фреймворк не является production-ready реализацией и не предназначен для коммерческого использования." . PHP_EOL . PHP_EOL
        );
        $output->success(" Доступные опции:" . PHP_EOL);

        $formattedOptions = [];

        foreach ($this->inputOptions->getPlugins() as $plugin) {
            $formattedOptions[] = "   \033[32m {$plugin::getPluginName()} \033[0m - {$plugin::getDescription()}" . PHP_EOL;
        }

        $output->text(implode("", $formattedOptions)  . PHP_EOL);
        $output->success(" Вызов:" . PHP_EOL);
        $output->text("   команда [аргументы] [опции]" . PHP_EOL . PHP_EOL);
        $output->text(" Доступные команды:" . PHP_EOL);

        $formattedCommands = [];

        foreach ($this->inputOptions->getCommandMap() as $name => $command) {
            if ($name !== static::$commandName) {
                $formattedCommands[] = " \033[32m $name \033[0m - {$command::getDescription()}" . PHP_EOL;
            }
        }

        $output->text(implode("", $formattedCommands) . PHP_EOL);
        $output->setMessage('');
        $output->setStatusCode(0);
    }
}
