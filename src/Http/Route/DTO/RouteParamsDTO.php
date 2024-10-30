<?php

namespace Craft\Http\Route\DTO;

class RouteParamsDTO
{
    public function __construct(
        public string  $name,
        public ?string $value,
        public array   $specifiers
    ) {
    }
}