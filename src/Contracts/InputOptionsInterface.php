<?php

namespace Craft\Contracts;

interface InputOptionsInterface
{
    /**
     * @return array
     */
    public function getOptions(): array;

    /**
     * @return array
     */
    public function getCommandMap(): array;

    /**
     * @return array
     */
    public function getPlugins(): array;
}
