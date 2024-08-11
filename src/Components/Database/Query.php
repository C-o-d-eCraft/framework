<?php

namespace Craft\Components\Database;

use Craft\Contracts\QueryInterface;

class Query implements QueryInterface
{
    private array $select = [];
    private array $from = [];
    private array $where = [];
    private array $join = [];
    private array $orderBy = [];
    private ?int $limit = null;
    private ?int $offset = null;

    /**
     * @param string|array $fields
     *
     * @return self
     */
    public function select(array|string ...$fields): self
    {
        $this->select = is_array($fields[0]) ? $fields[0] : $fields;
        
        return $this;
    }

    /**
     * @param string|array $tables
     *
     * @return self
     */
    public function from(array|string ...$tables): self
    {
        $this->from = is_array($tables[0]) ? $tables[0] : $tables;

        return $this;
    }

    /**
     * @param string|array $condition
     *
     * @return self
     */
    public function where(array|string $condition): self
    {
        if (is_array($condition) === true) {
            foreach ($condition as $key => $value) {
                if ($value instanceof self === true) {
                    $this->where[] = "$key = (" . $value->build() . ")";
                    continue;
                }

                $this->where[] = "$key = " . (is_numeric($value) ? $value : "'" . addslashes((string) $value) . "'");
            }

            return $this;
        }

        $this->where[] = $condition;

        return $this;
    }

    /**
     * @param string $column
     * @param array $values
     *
     * @return self
     */
    public function whereIn(string $column, array $values): self
    {
        $escapedValues = array_map(function($value) {
            return is_numeric($value) ? $value : "'" . addslashes((string) $value) . "'";
        }, $values);

        $this->where[] = "$column IN (" . implode(', ', $escapedValues) . ")";

        return $this;
    }

    /**
     * @param string $type
     * @param string $table
     * @param string $on
     *
     * @return self
     */
    public function join(string $type, string $table, string $on): self
    {
        $this->join[] = strtoupper($type) . " JOIN $table ON $on";

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return self
     */
    public function orderBy(array $columns): self
    {
        foreach ($columns as $column => $direction) {
            $this->orderBy[] = "$column " . (strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC');
        }

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return self
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @return string
     */
    public function build(): string
    {
        $sql = [];

        $sql[] = 'SELECT ' . implode(', ', $this->select);
        $sql[] = 'FROM ' . implode(', ', $this->from);

        if (empty($this->join) === false) {
            $sql[] = implode(' ', $this->join);
        }

        if (empty($this->where) === false) {
            $sql[] = 'WHERE ' . implode(' AND ', $this->where);
        }

        if (empty($this->orderBy) === false) {
            $sql[] = 'ORDER BY ' . implode(', ', $this->orderBy);
        }

        if ($this->limit !== null) {
            $sql[] = 'LIMIT ' . $this->limit;
        }

        if ($this->offset !== null) {
            $sql[] = 'OFFSET ' . $this->offset;
        }

        return implode(' ', $sql);
    }

    /**
     * @param PDO $db
     *
     * @return array|null
     */
    public function all(\PDO $db): array
    {
        $sql = $this->build();
        $stmt = $db->query($sql);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param PDO $db
     *
     * @return array
     */
    public function one(\PDO $db): ?array
    {
        $sql = $this->build();
        $stmt = $db->query($sql);

        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * @param PDO $db
     *
     * @return array
     */
    public function column(\PDO $db): array
    {
        $sql = $this->build();
        $stmt = $db->query($sql);

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @param PDO $db
     *
     * @return mixed
     */
    public function scalar(\PDO $db): mixed
    {
        $sql = $this->build();
        $stmt = $db->query($sql);

        return $stmt->fetchColumn();
    }
}
