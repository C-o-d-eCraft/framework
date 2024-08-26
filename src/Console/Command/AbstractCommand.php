<?php

namespace Craft\Console\Command;

use Craft\Contracts\CommandInterface;
use Craft\Contracts\InputInterface;
use Craft\Contracts\OutputInterface;
use LogicException;

class AbstractCommand implements CommandInterface
{
    /**
     * @var string
     */
    protected static string $commandName = 'Наименование не добавлено';

    /**
     * @var string
     */
    protected static string $description = 'Описание не добавлено';

    /**
     * Возвращает первую часть команды до первого пробела.
     *
     * @return string
     */
    public static function getCommandName(): string
    {
        return explode(' ', static::$commandName)[0];
    }

    /**
     * Возвращает описание команды.
     *
     * @return string
     */
    public static function getDescription(): string
    {
        return static::$description;
    }

    /**
     * Возвращает подробную информацию о команде
     * Пример использования:
     * 'calculator:calculate-modes {firstNumber:первое число} {?secondNumber:второе число=300}'
     *
     * на выходе получится:
     * [
     * "commandName" => "calculator:calculate-modes"
     * "description" => "Команда подбора последовательности режимов расчета для случайных чисел"
     * "arguments" => [
     * 0 => ["name" => "firstNumber"
     *       "info" => "первое число"
     *       "required" => true
     *       "defaultValue" => null]
     *
     * 1 => ["name" => "secondNumber"
     *       "info" => "второе число"
     *       "required" => false
     *       "defaultValue" => "300"]]
     * ]
     * @return array
     */
    public static function getFullCommandInfo(): array
    {
        $parts = explode(' ', static::$commandName, 2);
        $commandName = $parts[0];
        $argumentsString = $parts[1] ?? '';

        if (empty($argumentsString) === true) {
            return [
                'commandName' => $commandName,
                'description' => static::$description,
                'arguments' => [],
            ];
        }

        preg_match_all('/\{([^}]+)}/', $argumentsString, $matches);
        $arguments = [];

        foreach ($matches[1] as $arg) {
            $isRequired = $arg[0] !== '?';
            if ($isRequired === false) {
                $arg = substr($arg, 1);
            }

            $parts = explode(':', $arg, 2);
            $name = $parts[0];
            $info = null;
            $defaultValue = null;

            if (isset($parts[1])) {
                $infoParts = explode('=', $parts[1], 2);
                $info = $infoParts[0];

                if (isset($infoParts[1])) {
                    $defaultValue = $infoParts[1];
                }
            }

            $arguments[] = [
                'name' => $name,
                'info' => $info,
                'required' => $isRequired,
                'defaultValue' => $defaultValue,
            ];
        }

        return [
            'commandName' => $commandName,
            'description' => static::$description,
            'arguments' => $arguments,
        ];
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output): void
    {
        throw new LogicException("Не реализован основной метод для команды " . static::getCommandName());
    }

}