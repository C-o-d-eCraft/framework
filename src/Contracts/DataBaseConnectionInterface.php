<?php

namespace Craft\Contracts;

interface DataBaseConnectionInterface
{
    /**
     * выполняет SQL-запрос и возвращает количество затронутых строк.
     *
     * @param string $query
     * @param array $bindings
     * @return array|false
     */
    public function exec(string $query, array $bindings = []): array|false;

    /**
     * выполняет запрос SELECT и возвращает массив результатов.
     *
     * @param string $tableName
     * @param array $columns
     * @param string|null $condition
     * @param array $bindings
     * @return array|false
     */
    public function select(string $tableName, array $columns, string $condition = null, array $bindings = []): array|false;

    /**
     * выполняет запрос SELECT и возвращает одну запись (первую найденную).
     *
     * @param string $tableName
     * @param array $columns
     * @param string|null $condition
     * @param array $bindings
     * @return array|false
     */
    public function selectOne(string $tableName, array $columns, string $condition = null, array $bindings = []): array|false;

    /**
     * выполняет запрос INSERT и возвращает количество затронутых строк.
     *
     * @param string $tableName
     * @param array $values
     * @param string|null $condition
     * @param array $bindings
     * @return int
     */
    public function insert(string $tableName, array $values, string $condition = null, array $bindings = []): int;

    /**
     * выполняет запрос UPDATE и возвращает количество затронутых строк.
     *
     * @param string $tableName
     * @param array $values
     * @param string|null $condition
     * @param array $bindings
     * @return int
     */
    public function update(string $tableName, array $values, string $condition = null, array $bindings = []): int;

    /**
     * выполняет запрос DELETE и возвращает количество затронутых строк.
     *
     * @param string $tableName
     * @param string $condition
     * @param array $bindings
     * @return int
     */
    public function delete(string $tableName, array $condition, array $bindings = []): int;
}
