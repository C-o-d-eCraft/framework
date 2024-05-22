<?php

namespace Craft\Components\DatabaseConnection;

use Craft\Contracts\DataBaseConnectionInterface;
use PDO;

class DBConnection implements DataBaseConnectionInterface
{
    /**
     * @var PDO
     */
    public  PDO $pdo;

    /**
     * @var string|null
     */
    private ?string $query = null;

    /**
     * @var array
     */
    private array $bindings = [];

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['dsn'], $config['username'], $config['password'], $config['options']) === false) {
            throw new \InvalidArgumentException("Неверная конфигурация базы данных");
        }

        try {
            $this->pdo = new PDO(
                $config['dsn'],
                $config['username'],
                $config['password'],
                $config['options']
            );

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            throw new \RuntimeException("Не удалось подключиться к базе данных: " . $e->getMessage());
        }
    }
}
