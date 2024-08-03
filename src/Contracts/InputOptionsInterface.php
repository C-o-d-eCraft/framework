<?php

namespace Craft\Contracts;

interface InputOptionsInterface
{
    public function getOptions(): array;
    
    public function getCommandMap(): array;
    
    public function getPlugins(): array;
}