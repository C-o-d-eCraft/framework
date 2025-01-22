<?php

namespace Craft\Contracts;

use Fschmtt\Keycloak\Collection\UserCollection;
use Fschmtt\Keycloak\Representation\User;

interface KeycloakClientInterface
{
    public function __construct(array $config);

    /**
     * @param string $userId
     * @return User|null
     */
    public function getUser(string $userId): ?User;

    /**
     * @param array $searchParam
     * @return UserCollection
     */
    public function searchUsersByParams(array $searchParam): UserCollection;
}
