<?php

namespace Craft\Contracts;

interface JwtConfigInterface
{
    public function __construct(array $config);

    public function getValue(string $name): string;
}
