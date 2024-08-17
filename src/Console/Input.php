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
