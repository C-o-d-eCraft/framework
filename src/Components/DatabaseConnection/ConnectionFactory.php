<?php

namespace Craft\Components\DatabaseConnection;

use Craft\Contracts\ConnectionFactoryInterface;
use Craft\Contracts\DataBaseConnectionInterface;

class ConnectionFactory implements ConnectionFactoryInterface
{
    /**
     * @param array $config
     * @return DataBaseConnectionInterface
     */
    public function createConnection(array $config): DataBaseConnectionInterface
    {
        return match ($config['driver']) {
            'mysql' => new MySqlConnection($config),
        };
    }
}
