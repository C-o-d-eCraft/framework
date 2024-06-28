<?php

namespace Craft\Contracts;

interface DebugTagGeneratorInterface
{
    public function init(): void;
    public function refreshTag(): void;
}