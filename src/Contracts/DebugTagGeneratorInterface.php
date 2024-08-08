<?php

namespace Craft\Contracts;

interface DebugTagGeneratorInterface
{
    /**
     * @return void
     */
    public function init(): void;

    /**
     * @return void
     */
    public function refreshTag(): void;
}
