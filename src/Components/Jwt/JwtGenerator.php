<?php

namespace Craft\Components\Jwt;

use Craft\Contracts\JwtConfigInterface;

readonly class JwtGenerator
{
    public function __construct(
        private JwtConfigInterface $config,
    )
    {}
    /**
     * Генерация JWT токена
     *
     * @param string $sub Идентификатор пользователя
     * @return string Сгенерированный JWT токен
     */
    public function generateToken(string $sub): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + (int)$this->config->getValue('lifetime');

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'sub' => $sub,
        ];

        $encoder = new JwtEncoder($this->config);

        return $encoder->encode($payload);
    }
}
