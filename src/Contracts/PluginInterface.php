<?php

namespace Craft\Contracts;

interface PluginInterface
{
    /**
     * Регистрирует плагин в eventDispatcher
     *
     * @return void
     */
    public function init(): void;
}
