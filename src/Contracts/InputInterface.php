<?php

namespace Craft\Contracts;

interface InputInterface
{
    /**
     * @return array
     */
    public function getArguments(): array;

    /**
     * @return string|null
     */
    public function getCommandNameSpace(): ?string;

    /**
     * @param array $arguments
     * @return void
     */
    public function setArguments(array $arguments): void;

    /**
     * @return array
     */
    public function getOptions(): array;

}
