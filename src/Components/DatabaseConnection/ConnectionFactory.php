<?php

namespace Craft\Components\DatabaseConnection;

use Craft\Contracts\ConnectionFactoryInterface;
use Craft\Contracts\DataBaseConnectionInterface;
use Craft\Components\QueryBuilder\QueryBuilder;

class ConnectionFactory implements ConnectionFactoryInterface
{
    /**
     * @param array $config
     *
     * @return DataBaseConnectionInterface
     */
    public function createConnection(array $config): DataBaseConnectionInterface
    {
        return match ($config['driver']) {
            'mysql' => new QueryBuilder($config),
        };
    }
}
