<?php

namespace Craft\Contracts;

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
    public static function getFullCommandInfo(): array;

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output): void;
}
