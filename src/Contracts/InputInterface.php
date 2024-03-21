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
}
