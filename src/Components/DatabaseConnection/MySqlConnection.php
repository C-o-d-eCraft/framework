<?php

namespace Craft\Components\DatabaseConnection;

use Craft\Contracts\DataBaseConnectionInterface;
use PDO;

readonly class MySqlConnection implements DataBaseConnectionInterface
{
    /**
     * @var PDO
     */
    private PDO $pdo;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->pdo = new PDO(
            $config['dsn'],
            $config['username'],
            $config['password'],
            $config['options']
        );
    }

    /**
     * @param string $query
     * @param array $bindings
     * @return array|false
     */
    public function exec(string $query, array $bindings = []): array|false
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($bindings);

        return $statement->fetchAll();
    }

    /**
     * @param string $tableName
     * @param array $columns
     * @param string|null $condition
     * @param array $bindings
     * @return array|false
     */
    public function select(string $tableName, array $columns, string $condition = null, array $bindings = []): array|false
    {
        $sql = 'SELECT ' . implode(', ', $columns) . ' FROM ' . $tableName;

        if (empty($condition) === false) {
            $sql .= " WHERE $condition";
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute($bindings);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $tableName
     * @param array $columns
     * @param string|null $condition
     * @param array $bindings
     * @return array|false
     */
    public function selectOne(string $tableName, array $columns, string $condition = null, array $bindings = []): array|false
    {
        $result = $this->select($tableName, $columns, $condition, $bindings);

        if ($result === false) {
            return false;
        }

        return $result[0];
    }

    /**
     * @param string $tableName
     * @param array $values
     * @param string|null $condition
     * @param array $bindings
     * @return int
     */
    public function insert(string $tableName, array $values, string $condition = null, array $bindings = []): int
    {
        $columns = implode(', ', array_keys($values));
        $placeholders = implode(', ', array_fill(0, count($values), '?'));

        $sql = "INSERT INTO $tableName ($columns) VALUES ($placeholders)";

        $statement = $this->pdo->prepare($sql);
        $statement->execute(array_values($values));

        return $statement->rowCount();
    }

    /**
     * @param string $tableName
     * @param array $values
     * @param string|null $condition
     * @param array $bindings
     * @return int
     */
    public function update(string $tableName, array $values, string $condition = null, array $bindings = []): int
    {
        $setClause = implode (', ', array_map(fn ($column) => "$column .  = ?", array_keys($values)));

        $sql = "UPDATE $tableName SET $setClause";

        if (empty($condition) === false) {
            $sql .= " WHERE $condition";
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute(array_values($values));

        return $statement->rowCount();
    }

    /**
     * @param string $tableName
     * @param string $condition
     * @param array $bindings
     * @return int
     */
    public function delete(string $tableName, array $condition, array $bindings = []): int
    {
        $whereClause = '';
        $bindings = [];

        foreach ($condition as $column => $value) {
            $whereClause .= "$column = :$column AND ";
            $bindings[":$column"] = $value;
        }

        $whereClause = rtrim($whereClause, ' AND ');

        $sql = "DELETE FROM $tableName WHERE $whereClause";

        $statement = $this->pdo->prepare($sql);
        $statement->execute($bindings);

        return $statement->rowCount();
    }
}
