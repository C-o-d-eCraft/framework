<?php

namespace Craft\Console\Command;

class CommandInfoDTO
{
    public function __construct(
        public string $commandName = '',
        public string $description = '',
        public array $arguments = []
    ) {
    }
}