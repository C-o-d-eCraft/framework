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
     * @param array $data Данные пользователя для включения в payload
     * @return string Сгенерированный JWT токен
     */
    public function generateToken(array $data): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + (int)$this->config->getValue('lifetime');

        $userData = [
            'email' => $data['email'],
            'id' => $data['id'],
            'name' => $data['name'],
            'status' => $data['status'],
        ];

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'data' => $userData,
        ];

        $encoder = new JwtEncoder($this->config);

        return $encoder->encode($payload);
    }
}
