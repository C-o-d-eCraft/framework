<?php

namespace Craft\Contracts;

interface DataBaseConnectionInterface
{
    /**
     * @param string $tableName
     * @param array $columns
     * @param string|null $condition
     * @param array $bindings
     * @return array|false
     */
    public function select(string|array $columns): self;

    /**
     * @param string $table
     * @return self
     */
    public function from(string $table): self;

    /**
     * @param string|array $condition
     * @return self|false
     */
    public function where(string|array $condition): self|false;

    /**
     * @param int $limit
     * @return self
     */
    public function innerJoin(string $table, string $condition): self;

    /**
     * @param string $table
     * @param array $values
     * @param string|null $condition
     * @param array $bindings
     * @return int
     */
    public function insert(string $table, array $values, string $condition = null, array $bindings = []): int;

    /**
     * @param string $table
     * @param array $values
     * @param string|null $condition
     * @param array $bindings
     * @return int
     */
    public function update(string $table, array $values, ?array $condition = null, array $bindings = []): int;

    /**
     * @param string $table
     * @param string $condition
     * @param array $bindings
     * @return int
     */
    public function delete(string $table, array $condition, array $bindings = []): int;

    /**
     * @return array|false
     */
    public function one(): array|false;

    /**
     * @return array|false
     */
    public function all(): array|false;
}
