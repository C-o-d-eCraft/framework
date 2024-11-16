<?php

namespace Craft\Components\Jwt;

use Craft\Contracts\JwtConfigInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

readonly class JwtDecoder
{
    public function __construct(
        private JwtConfigInterface $config,
    )
    {}

    /**
     * Валидация и декодирование JWT токена
     *
     * SignatureInvalidException — выбрасывается при некорректной подписи.
     * BeforeValidException — если токен недействителен из-за указания nbf (Not Before) времени.
     * ExpiredException — если токен просрочен из-за указания exp (Expiration) времени.
     *
     * @param string $token Токен JWT
     * @return string Идентификатор пользователя из JWT токена
     */
    public function decode(string $token): string
    {
        $decodedToken = JWT::decode(
            $token,
            new Key($this->config->getValue('secretKey'), $this->config->getValue('algorithm')));

        return $decodedToken->sub;
    }
}
