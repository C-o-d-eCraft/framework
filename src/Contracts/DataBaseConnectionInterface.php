<?php

namespace Craft\Contracts;

use Craft\Components\Database\Query;

interface DataBaseConnectionInterface
{
    /**
     * @param Query $query
     *
     * @return array
     */
    public function select(Query $query): array;

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
