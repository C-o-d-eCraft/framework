<?php

namespace Craft\Components\DatabaseConnection;

use Craft\Contracts\ConnectionFactoryInterface;
use Craft\Contracts\DataBaseConnectionInterface;

class MySqlConnectionFactory implements ConnectionFactoryInterface
{
    /**
     * @param array $config
     * @return DataBaseConnectionInterface
     */
    public function createConnection(array $config): DataBaseConnectionInterface
    {
        return match ($config['driver']) {
            'mysql' => new DBConnection($config),
        };
    }
}
