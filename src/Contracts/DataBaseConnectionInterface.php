<?php

namespace Craft\Contracts;

use Craft\Components\Database\MySql\QueryBuilder;

interface DataBaseConnectionInterface
{
    /**
     * @param QueryBuilder $query
     *
     * @return array
     */
    public function select(QueryBuilder $query): array;

    /**
     * @param string $table
     * @param array $data
     * @param array $condition
     *
     * @return int
     */
    public function update(string $table, array $data, array $condition): int;

    /**
     * @param string $table
     * @param array $data
     *
     * @return int
     */
    public function insert(string $table, array $data): int;

    /**
     * @param string $table
     * @param array $condition
     *
     * @return int
     */
    public function delete(string $table, array $condition): int;
}
