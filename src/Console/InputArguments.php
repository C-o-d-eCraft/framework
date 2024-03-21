<?php

namespace Craft\Console;

class InputArguments
{
    /**
     * @var string
     */
    public string $name = '';

    /**
     * @var bool
     */
    public bool $required = false;

    /**
     * @var int|null
     */
    public ?int $defaultValue = null;

    /**
     * @param string $argument
     */
    public function __construct(string $argument)
    {
        $this->parseArguments($argument);
    }

    /**
     * @param string $argument
     *
     * @return void
     */
    public function parseArguments(string $argument): void
    {
        $this->required = true;

        if ($argument[0] === '?') {
            $argument = substr($argument, 1);
            $this->required = false;
        }

        $equalsPosition = strpos($argument, "=");

        if ($equalsPosition !== false) {

            $this->defaultValue = (int) substr($argument, $equalsPosition + 1);

            $argument = substr($argument, 0, $equalsPosition);
        }

        $this->name = $argument;
    }
}
