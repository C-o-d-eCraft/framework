<?php

namespace Craft\Components\Database;

use Craft\Contracts\DataBaseConnectionInterface;
use PDO;

class QueryBuilder implements DataBaseConnectionInterface
{
    /**
     * @var PDO
     */
    public PDO $pdo;

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

    /**
     * @param Query $query
     *
     * @return array
     */
    public function select(Query $query): array
    {
        return $query->all($this->pdo);
    }

    public function selectOne(Query $query): ?array
    {
        return $query->one($this->pdo);
    }

    /**
     * @param Query $query
     *
     * @return array
     */
    public function selectColumn(Query $query): array
    {
        return $query->column($this->pdo);
    }

    /**
     * @param Query $query
     *
     * @return mixed
     */
    public function selectScalar(Query $query): mixed
    {
        return $query->scalar($this->pdo);
    }

    /**
     * @param string $table
     * @param array $data
     * @param array $condition
     *
     * @return int
     */
    public function update(string $table, array $data, array $condition): int
    {
        $set = [];
        $params = [];

        foreach ($data as $key => $value) {
            $set[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $where = [];

        foreach ($condition as $key => $value) {
            if ($value instanceof Query === true) {
                $where[] = "$key = (" . $value->build() . ")";
            }
            if ($value instanceof Query === false) {
                $where[] = "$key = :cond_$key";
                $params[":cond_$key"] = $value;
            }
        }

        $sql = "UPDATE $table SET " . implode(', ', $set) . " WHERE " . implode(' AND ', $where);
        $sql = $this->interpolateQuery($sql, $params);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    /**
     * @param string $table
     * * @param array $data
     *
     * @return int
     */
    public function insert(string $table, array $data): int
    {
        $keys = array_keys($data);
        $fields = implode(', ', $keys);

        $params = [];

        foreach ($data as $key => $value) {
            if ($value instanceof Query === true) {
                $params[$key] = '(' . $value->build() . ')';
            }
            if ($value instanceof Query === false && is_string($value) === false) {
                $params[$key] = $value;
            }
            if ($value instanceof Query === false && is_string($value) === true) {
                $params[$key] = "'" . $value . "'";
            }
        }

        $sql = "INSERT INTO $table ($fields) VALUES (" . implode(', ', $params) . ")";
        $sql = $this->interpolateQuery($sql, $params);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * @param string $table
     * @param array $condition
     *
     * @return int
     */
    public function delete(string $table, array $condition): int
    {
        $where = [];
        $params = [];

        foreach ($condition as $key => $value) {
            if ($value instanceof Query) {
                $where[] = "$key = (" . $value->build() . ")";
                continue;
            }

            $where[] = "$key = :$key";
            $params[":$key"] = $value;
        }

        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $where);
        $sql = $this->interpolateQuery($sql, $params);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }

    /**
     * @param string $query
     * @param array $params
     *
     * @return string
     */
    private function interpolateQuery(string $query, array $params): string
    {
        $keys = [];
        $values = [];

        foreach ($params as $key => $value) {
            $keys[] = is_string($key) ? '/:' . $key . '/' : '/[?]/';

            if (is_numeric($value)) {
                $values[] = $value;
                continue;
            }

            if (is_bool($value)) {
                $values[] = $value ? 'TRUE' : 'FALSE';
                continue;
            }

            if (is_null($value)) {
                $values[] = 'NULL';
                continue;
            }

            if (is_string($value)) {
                $values[] = "'" . addslashes($value) . "'";
            }
        }

        $query = preg_replace($keys, $values, $query, 1);

        return $query;
    }
}
