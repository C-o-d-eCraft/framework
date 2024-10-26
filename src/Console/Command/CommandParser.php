<?php

namespace Craft\Console\Command;

class CommandParser
{
    public function __construct(
        private string $commandName,
        private string $description
    ) {
    }

    /**
     * @return CommandInfoDTO
     */
    public function getFullCommandInfo(): CommandInfoDTO
    {
        $parts = explode(' ', $this->commandName, 2);
        $commandName = $parts[0];
        $argumentsString = $parts[1] ?? '';

        if (empty($argumentsString) === true) {
            return new CommandInfoDTO($commandName, $this->description);
        }

        preg_match_all('/\{([^}]+)}/', $argumentsString, $matches);

        $arguments = array_map([$this, 'parseArgument'], $matches[1]);

        return new CommandInfoDTO($commandName, $this->description, $arguments);
    }

    /**
     * @param string $arg
     * @return array
     */
    private function parseArgument(string $arg): array
    {
        $isRequired = $this->isRequired($arg);
        $arg = $this->normalizeArgument($arg);

        [$name, $info] = $this->splitArgument($arg);
        [$info, $defaultValue] = $this->getDefaultValue($info);

        return [
            'name' => $name,
            'info' => $info,
            'required' => $isRequired,
            'defaultValue' => $defaultValue,
        ];
    }

    /**
     * @param string $arg
     * @return bool
     */
    private function isRequired(string $arg): bool
    {
        return $arg[0] !== '?';
    }

    /**
     * @param string $arg
     * @return string
     */
    private function normalizeArgument(string $arg): string
    {
        return $arg[0] === '?' ? substr($arg, 1) : $arg;
    }

    /**
     * @param string $arg
     * @return array
     */
    private function splitArgument(string $arg): array
    {
        $parts = explode(':', $arg, 2);

        return [$parts[0], $parts[1] ?? null];
    }

    /**
     * @param string|null $info
     * @return array|null[]
     */
    private function getDefaultValue(?string $info): array
    {
        if ($info === null) {
            return [null, null];
        }

        $infoParts = explode('=', $info, 2);

        return [$infoParts[0], $infoParts[1] ?? null];
    }
}
