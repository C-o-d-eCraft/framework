<?php

namespace Craft\Contracts;

interface QueryBuilderInterface
{
    /**
     * @param string|array $fields
     *
     * @return self
     */
    public function select(array|string ...$fields): self;

    /**
     * @param string|array $tables
     *
     * @return self
     */
    public function from(array|string ...$tables): self;

    /**
     * @param string|array $condition
     *
     * @return self
     */
    public function where(array|string $condition): self;

    /**
     * @param string $type
     * @param tring $table
     * @param string $on
     *
     * @return self
     */
    public function join(string $type, string $table, string $on): self;

    /**
     * @param array $columns
     *
     * @return self
     */
    public function orderBy(array $columns): self;

    /**
     * @param int $limit
     *
     * @return self
     */
    public function limit(int $limit): self;

    /**
     * @param int $offset
     *
     * @return self
     */
    public function offset(int $offset): self;

    /**
     * @return string
     */
    public function build(): string;

    /**
     * @param PDO $db
     *
     * @return array|null
     */
    public function one(\PDO $db): ?array;

    /**
     * @param PDO $db
     *
     * @return array
     */
    public function all(\PDO $db): array;

    /**
     * @param PDO $db
     *
     * @return array
     */
    public function column(\PDO $db): array;

    /**
     * @param PDO $db
     *
     * @return mixed
     */
    public function scalar(\PDO $db): mixed;
}
