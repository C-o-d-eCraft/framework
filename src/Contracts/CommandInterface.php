<?php

namespace Craft\Contracts;

use Craft\Console\Command\CommandInfoDTO;

interface CommandInterface
{
    /**
     * @return string
     */
    public static function getCommandName(): string;

    /**
     * @return string
     */
    public static function getDescription(): string;

    /**
     * @return array
     */
    public static function getFullCommandInfo(): CommandInfoDTO;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output): void;
}
