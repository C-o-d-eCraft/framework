<?php

namespace Framework\Components\DatabaseConnection;

use Framework\Contracts\ConnectionFactoryInterface;
use Framework\Contracts\DataBaseConnectionInterface;

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
