<?php

namespace Craft\Components\QueryBuilder;

use Craft\Contracts\DataBaseConnectionInterface;
use Craft\Contracts\QueryBuilderInterface;
use \PDO;

class QueryBuilderMysql implements QueryBuilderInterface
{
    /**
     * @var PDO
     */
    private $pdo;

    private ?string $query = null;

    /**
     * @var array
     */
    private array $bindings = [];

        public function __construct(private DataBaseConnectionInterface $db) {
            $this->pdo = $db->pdo;
        }

    /**
     * @param string|array $columns
     * @return $this
     */
    public function select(string|array $columns): self
    {
        if (is_array($columns)) {
            $columns = implode(', ', $columns);
        }

        $this->query = "SELECT $columns";

        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function from(string $table): self
    {
        $this->query .= " FROM $table";
        return $this;
    }

    /**
     * @param string|array $condition
     * @return $this
     */
    public function where(string|array $condition): self|false
    {
        if (is_array($condition) === false) {
            return false;
        }

        $conditions = [];

        foreach ($condition as $column => $value) {
            $conditions[] = "$column = ?";
            $this->bindings[] = $value;
        }

        $condition = implode(' AND ', $conditions);

        $this->query .= " WHERE $condition";

        return $this;
    }

    /**
     * @param string $table
     * @param string $condition
     * @return $this
     */
    public function innerJoin(string $table, string $condition): self
    {
        $this->query .= " INNER JOIN $table ON $condition";
        return $this;
    }

    /**
     * @param string $table
     * @param array $values
     * @param string|null $condition
     * @param array $bindings
     * @return int
     */
    public function insert(string $table, array $values, string $condition = null, array $bindings = []): int
    {
        $columns = implode(', ', array_keys($values));
        $placeholders = implode(', ', array_fill(0, count($values), '?'));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

        $statement = $this->pdo->prepare($sql);
        $statement->execute(array_values($values));

        return $statement->rowCount();
    }

    /**
     * @param string $table
     * @param array $values
     * @param string|null $condition
     * @param array $bindings
     * @return int
     */
    public function update(string $table, array $values, ?array $condition = null, array $bindings = []): int
    {
        $setClause = implode(', ', array_map(fn($column) => "$column = ?", array_keys($values)));
        $sql = "UPDATE $table SET $setClause";

        if (!empty($condition)) {
            $whereClause = implode(' AND ', array_map(fn($column) => "$column = ?", array_keys($condition)));
            $sql .= " WHERE $whereClause";
            $bindings = array_merge($bindings, array_values($condition));
        }

        $statement = $this->pdo->prepare($sql);
        $statement->execute(array_merge(array_values($values), $bindings));

        return $statement->rowCount();
    }

    /**
     * @param string $table
     * @param string $condition
     * @param array $bindings
     * @return int
     */
    public function delete(string $table, array $condition, array $bindings = []): int
    {
        $whereClause = '';
        $bindings = [];

        foreach ($condition as $column => $value) {
            $whereClause .= "$column = :$column AND ";
            $bindings[":$column"] = $value;
        }

        $whereClause = rtrim($whereClause, ' AND ');

        $sql = "DELETE FROM $table WHERE $whereClause";

        $statement = $this->pdo->prepare($sql);
        $statement->execute($bindings);

        return $statement->rowCount();
    }

    /**
     * @return array|false
     */
    public function one(): array|false
    {
        $statement = $this->pdo->prepare($this->query);
        $statement->execute($this->bindings);
        $this->reset();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @return array|false
     */
    public function all(): array|false
    {
        $statement = $this->pdo->prepare($this->query);
        $statement->execute($this->bindings);
        $this->reset();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Для написания любого запроса без вспомогательных методов
     * @return array|false
     */
    public function query(string $query): array|false
    {
        $this->query = $query;
        $statement = $this->pdo->prepare($this->query);
        $statement->execute($this->bindings);
        $this->reset();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return void
     */
    private function reset(): void
    {
        $this->query = null;
        $this->bindings = [];
    }

}