<?php

namespace Craft\Components\Jwt;

use Craft\Contracts\JwtConfigInterface;

class JwtConfig implements JwtConfigInterface
{
    public array $secretStorage;
    public function __construct(
        array $config = ['secretKey' => 'secret', 'algorithm' => 'HS256', 'lifetime' => 3600])
    {
        $this->secretStorage = $config;
    }

    public function getValue(string $name): string
    {
        return $this->secretStorage[$name];
    }
}
