<?php

namespace Craft\Console;

use Craft\Contracts\InputInterface;

class Input implements InputInterface
{
    /**
     * @var array
     */
    private array $arguments = [];

    /**
     * @return string|null
     */
    private ?string $nameSpace = null;

    /**
     * @var array
     */
    private array $options = [];

    /**
     * @param array|null $argv
     */
    public function __construct(private array|null $argv)
    {
        $this->parseInput();
    }

    /**
     * @return string|null
     *
     */
    public function getCommandNameSpace(): ?string
    {
        return $this->nameSpace;
    }

    /**
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * @param array $arguments
     * @return void
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string $commandName
     * @return array
     */
    public function parseCommandArguments(string $commandName): array
    {
        $pattern = '/\{([^}]+)}/';

        preg_match_all($pattern, $commandName, $matches);

        $arguments = [];

        if (empty($matches) === true) {
            return [];
        }

        foreach ($matches[1] as $argument) {
            $arguments[] = new InputArguments($argument);
        }

        return $arguments;
    }

    /**
     * @param array $commandArguments
     * @return void
     */
    public function comparisonArguments(array $commandArguments): void
    {
        $inputArguments = $this->getArguments();

        if (empty($commandArguments) === true) {
            return;
        }

        $expectedParams = count($commandArguments);
        $actualParams = count($inputArguments);

        if ($actualParams > $expectedParams) {
            throw new LogicException('Избыточное количество аргументов');
        }

        $argumentIndex = 0;
        $enteredArguments = [];

        foreach ($commandArguments as $argument) {
            $paramName = $argument->name;
            $defaultValue = $argument->defaultValue;
            $paramsValue = $inputArguments[$argumentIndex] ?? $defaultValue;

            if ($paramsValue === null && $defaultValue === null) {
                throw new LogicException("\"{$paramName}\" Аргумент обязателен для ввода");
            }

            $enteredArguments[$paramName] = $paramsValue;
            $argumentIndex++;
        }

        $this->setArguments($enteredArguments);
    }

    /**
     * @return void
     */
    private function parseInput(): void
    {
        array_shift($this->argv);

        $this->nameSpace = array_shift($this->argv);

        foreach ($this->argv as $item) {
            if (str_starts_with($item, '--')) {
                $this->options[] = $item;

                continue;
            }

            $this->arguments[] = $item;
        }
    }
}
