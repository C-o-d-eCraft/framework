<?php

namespace Craft\Contracts;

interface IdentityInterface
{
    /**
     * Получает идентификационные данные авторизованного пользователя из заголовка запроса.
     *
     * @return array|null Возвращает массив с идентификационными данными пользователя или null, если пользователь не найден.
     */
    public function findIdentityFromHeaders(): string|null;

    public function getIdentityUser(): array|null;

    public function getIdentityUserId(): int|null;
}