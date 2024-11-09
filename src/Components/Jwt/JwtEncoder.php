<?php

namespace Craft\Components\Jwt;

use Craft\Contracts\JwtConfigInterface;
use Firebase\JWT\JWT;

readonly class JwtEncoder
{
    public function __construct(
        private JwtConfigInterface $config,
    )
    {}
    /**
     * Сборка JWT токена
     *
     * @param array $payload Данные пользователя для включения в payload
     * @return string Сгенерированный JWT токен
     */
    public function encode(array $payload): string
    {
        return JWT::encode(
            $payload,
            $this->config->getValue('secretKey'),
            $this->config->getValue('algorithm')
        );
    }
}
