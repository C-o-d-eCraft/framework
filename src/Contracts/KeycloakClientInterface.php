<?php

namespace Craft\Contracts;

interface KeycloakClientInterface
{
    public function __construct(array $config);

    public function getAllUsers();

    public function getUser(string $userId);

    public function searchUserByParams(array $searchParam);
}
