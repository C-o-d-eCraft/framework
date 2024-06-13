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

    public function __construct(private DataBaseConnectionInterface $db)
    {
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
     * @return int
     * insert into prices (tonnage_id, month_id, raw_type_id, price)
     * values ((select t.id from tonnages as t where value = :tonnage),
     * (select m.id from months as m where name = :month),
     * (select type.id from raw_types as type where name = :type),
     * (:price));
     */
    public function insert(string $table, array $values, ?string $condition = null, array $bindings = []): int
    {
        if (is_array($values[0]) === true && count($values[0]) === 3) {
            foreach ($values as $value) {
                $columns .= $value[0] . ',';
                $placeholders .= '(' . $value[1] . '? ),';
                $prepareParam [] = $value[2];
            }
            $columns = substr($columns, 0, -1);
            $placeholders = substr($placeholders, 0, -1);
        }

        if (is_array($values[0]) === false) {
            $columns = implode(', ', array_keys($values));
            $placeholders = implode(', ', array_fill(0, count($values), '?'));

            $prepareParam = array_values($values);
        }

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $statement = $this->pdo->prepare($sql);
        $statement->execute($prepareParam);

        return $statement->rowCount();
    }

    /**
     * @param string $table Таблица БД
     * @param array $values значение для обновлнения в виде ключ => значение
     * @param array|null $condition опционально, условия где [ключ, любое условие прописанное кодом (сделано для подзапроса) ,значение]
     * @param array $bindings
     * @return int
     *
     * update prices
     * set
     * price = price:,
     * created_at = '2024-12-12 12:12:12'
     *
     * where tonnage_id = (select id from tonnages where value = value:)
     * and month_id = (select id from months where name = name:)
     * and raw_type_id = (select id from raw_types where name = name:);
     */
    public function update(string $table, array $values, ?array $condition = null, array $bindings = []): int
    {
        $setClause = implode(', ', array_map(fn($column) => "$column = ?", array_keys($values)));
        $sql = "UPDATE $table SET $setClause WHERE ";

        if (is_array($condition[0]) === true && count($condition[0]) === 3) {
            foreach ($condition as $cond) {
                $columns .= $cond[0] . ',';
                $placeholders .= $cond[0] . ' = (' . $cond[1] . '?) AND ';
                $prepareParam [] = $cond[2];
            }
            $placeholders = substr($placeholders, 0, -4);
            $bindings = array_merge(array_values($values), $prepareParam);

            $sql .= $placeholders;
        }
        if (is_array($condition[0]) === false) {
            $whereClause = implode(' AND ', array_map(fn($column) => "$column = ?", array_keys($condition)));
            $sql .= " WHERE $whereClause";
            $bindings = array_merge($bindings, array_values($condition));
        }

        $statement = $this->pdo->prepare($sql);

        $statement->execute($bindings);

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

        if (is_array($condition[0]) === true && count($condition[0]) === 3) {
            foreach ($condition as $cond) {
                $columns .= $cond[0] . ',';
                $placeholders .= $cond[0] . ' = (' . $cond[1] . ':' . $cond[0] . ') AND ';
                $prepareParam [] = $cond[2];
            }
            $placeholders = substr($placeholders, 0, -4);
            $bindings = array_merge(array_values($condition), $prepareParam);

            $sql = "DELETE FROM $table WHERE $placeholders";

            $statement = $this->pdo->prepare($sql);

            foreach ($condition as $cond) {
                $statement->bindParam(':' . $cond[0], $cond[2]);
            }

            $statement->execute();

            return $statement->rowCount();
        }

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
    public function execRaw(string $query): array|false
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