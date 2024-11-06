<?php

namespace Craft\Contracts;

interface IdentityInterface
{
    public function getTokenFromHeaders(): string|null;

    public function generateToken(array $data, int $expiresIn = 3600): string;

    public function validateToken(?string $token = null): array|false;

}