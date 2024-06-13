<?php

namespace Craft\Contracts;

interface QueryBuilderInterface
{
    /**
     * @param string|array $columns
     * @return self
     */
    public function select(string $table, string|array $columns): self;

    /**
     * @param string $table
     * @return self
     */
    public function table(string $table): self;

    /**
     * @return string
     */
    public function getQuery(): string|int;

    /**
     * @param string $column Атрибут таблицы
     * @param mixed $value Значение
     * @param string $operator Оператор, опциональный, по умолчанию =
     * @param string $connector
     * @return self
     */
    public function where(string $column,mixed $value,string $operator, string $connector): self|false;

    /**
     * @param int $limit
     * @return self
     */
    public function innerJoin(string $table, string $condition): self;

    /**
     * @param array $data
     */
    public function insert(string $table, array $data): int;

    /**
     * @param array $data
     * @return int
     */
    public function update(string $table, array $data): int;

    /**
     * @param string $table
     * @param string $condition
     * @param array $bindings
     * @return int
     */
    public function delete(string $table, array $data): int;

    /**
     * @return mixed
     */
    public function one(int $mode): mixed;

    /**
     * @return array|false
     */
    public function all(int $mode): mixed;

    /**
     * @return array|false
     */
    public function execRaw(string $query): array|false;
}