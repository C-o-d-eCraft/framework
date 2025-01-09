<?php

namespace Craft\Components\Jwt;

use Firebase\JWT\JWT;
use Exception;

readonly class JwtDecoder
{

    /**
     * Извлекает payload из JWT токена без проверки подписи.
     *
     * @param string $token Токен JWT
     * @return array Декодированный payload из JWT токена
     * @throws Exception Если токен некорректен
     */
    public function decode(string $token): array
    {
        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            throw new Exception('Некорректный формат JWT токена');
        }

        $decodedPayload = JWT::jsonDecode(JWT::urlsafeB64Decode($parts[1]));

        if (!is_object($decodedPayload)) {
            throw new Exception('Не удалось декодировать payload');
        }

        return (array)$decodedPayload;
    }

}
