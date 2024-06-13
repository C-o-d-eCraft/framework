<?php

namespace Craft\Components\QueryBuilder;

use Craft\Components\DatabaseConnection\MySqlConnection;
use Craft\Contracts\DataBaseConnectionInterface;

class Query
{
    /**
     * @var PDO
     */
    protected $pdo;
    protected $table;
    protected $query;
    protected $finalQuery;
    protected $params = [];
    protected $joins = [];
    protected $wheres = [];
    protected $prepareParamsFromQuery = [];
    protected $prepareQuery = [];


    public function __construct(DataBaseConnectionInterface $db)
    {
        $this->pdo = $db->pdo;
    }

    /**
     * @param bool $flag признак необходимости выполнения запроса (для подзапросов нужно явно указывать false), для обычных запросов можно не указывать, по дефолту true
     * @param int $mode можно указать режим выборки полученных данных по умолчанию \PDO::FETCH_ASSOC
     * @return mixed
     */
    public function one($flag = true ,$mode = \PDO::FETCH_ASSOC): mixed
    {
        $i = 0;
        $joinString = implode(' ', $this->joins);
        $whereString = implode(' ', array_slice($this->wheres, 0, -1));
        $this->query .= " {$joinString} WHERE {$whereString}";
        $this->wheres = [];

        $stmt = $this->pdo->prepare($this->query);

        foreach ($this->prepareParamsFromQuery as $key => $values) {
            foreach ($values as $param => $value) {
                    $i += 1;
                    $stmt->bindValue($i, $value);
            }
        }

        $this->finalQuery = $this->interpolateQuery($stmt->queryString, $this->params);
        $this->prepareQuery[] = ['finalQuery' => $this->finalQuery, 'prepareQuery' => $stmt->queryString, 'queryParams' => $this->params];

        if ($flag === true) {
            $stmt->execute();
            $this->reset();
            return $stmt->fetch($mode);
        }

        return $this;
    }

    /**
     * @param bool $flag признак необходимости выполнения запроса (для подзапросов нужно явно указывать false)? для обычных запросов можно не указывать, по дефолту true
     * @param int $mode можно указать режим выборки полученных данных по умолчанию \PDO::FETCH_ASSOC
     * @return mixed
     */
    public function all($flag = true ,$mode = \PDO::FETCH_ASSOC): mixed
    {
        $i = 0;
        $joinString = implode(' ', $this->joins);
        $whereString = implode(' ', array_slice($this->wheres, 0, -1)); // Удаляем последний коннектор

        $where = " WHERE ";
        if ($joinString === '' && $whereString === '') {
            $where = "";
        }
        $this->query .= $joinString . $where . $whereString;
        $this->wheres = [];

        $stmt = $this->pdo->prepare($this->query);

        foreach ($this->prepareParamsFromQuery as $key => $values) {
            foreach ($values as $param => $value) {
                    $i += 1;
                    $stmt->bindValue($i, $value);
            }
        }

        $this->finalQuery = $this->interpolateQuery($stmt->queryString, $this->params);
        $this->prepareQuery[] = ['finalQuery' => $this->finalQuery, 'prepareQuery' => $stmt->queryString, 'queryParams' => $this->params];

        if ($flag === true) {
            $stmt->execute();
            $this->reset();
            return $stmt->fetchAll($mode);
        }

        return $this;
    }


    /**Выполняет запрос и возвращает количество измененных строк
     *
     * @param $i для цикла
     * @return array|false
     */
    public function execute(): int
    {
        $i = 0;
        $params = [];
        $joinString = implode(' ', $this->joins);
        $whereString = implode(' ', array_slice($this->wheres, 0, -1)); // Удаляем последний коннектор

        $where = " WHERE ";
        if ($joinString === '' && $whereString === '') {
            $where = "";
        }
        $this->query .= $joinString . $where . $whereString;

        $this->wheres = [];

        $stmt = $this->pdo->prepare($this->query);

        foreach ($this->prepareParamsFromQuery as $key => $values) {
            foreach ($values as $param => $value) {

                    $i += 1;
                    $stmt->bindValue($i, $value);
                    $params[$i] = $value;
            }
        }

        $this->finalQuery = $this->interpolateQuery($stmt->queryString,$params);
        $this->prepareQuery[] = ['finalQuery' => $this->finalQuery, 'prepareQuery' => $stmt->queryString, 'queryParams' => $params];

        $stmt = $this->pdo->prepare($this->finalQuery);
        $stmt->execute();
        $this->reset();
        return $stmt->rowCount();
    }

    /**
     * @return void
     */
    private function reset(): void
    {
        $this->query = null;
        $this->params = [];
        $this->wheres = [];
        $this->joins = [];
        $this->prepareParamsFromQuery = [];
        $i = 0;
    }


    public function table(string $table): self
    {
        $this->table = $table;

        return $this;
    }

    /**
     * @param string|array $columns
     * @return $this
     */
    public function select(string $table, string|array $columns = '*'): self
    {
        if (is_array($columns) === true) {
            $columns = implode(', ', $columns);
        }
        $this->query = "SELECT {$columns} FROM {$table}";
        return $this;
    }

    /**
     * @param string $column таблица
     * @param mixed $value Значение
     * @param string $operator Оператор, опциональный, по умолчанию =
     * @param string $connector
     * @return self|false
     */
    public function where(string $column, string|Query $value, string $operator = '=', string $connector = 'AND'): self
    {
        if ($value instanceof Query === true) {
            $value = '(' . $value->finalQuery . ')';
        }
        $this->prepareParamsFromQuery[] = [$column => $value];
        $this->wheres[] = "{$column} {$operator} ?";
        $this->params[] = $value;
        $this->wheres[] = $connector;

        return $this;
    }

    public function insert(string $table, array $data): int
    {
        $keys = '';
        $values = '';
        foreach ($data as $key => $param) {

            if ($param instanceof Query === true) {
                $keys .= $key . ',';
                $values .= "({$param->finalQuery}),";
                continue;
            }

            $this->prepareParamsFromQuery[] = [$key => $param];

            $keys .= $key . ',';
            $values .= strripos($param, '=') ? "($param)" . ', ' : '? , ';
        }

        $keys = mb_substr($keys, 0, -1);
        $values = mb_substr($values, 0, -2);

        $this->query = "INSERT INTO {$table} ({$keys}) VALUES ({$values})";

        return $this->execute();
    }

    public function update(string $table, array $data): self
    {
        $keys = '';
        $values = '';
        foreach ($data as $key => $param) {

            if ($param instanceof Query === true) {
                $keys .= $key . ',';
                $values .= "({$param->finalQuery}),";
                continue;
            }

            $this->prepareParamsFromQuery[] = [$key => $param];

            $keys .= $key . ',';
            $values .= strripos($param, '=') ? "($param)" . ', ' : '? , ';
        }

        $keys = mb_substr($keys, 0, -1);
        $values = mb_substr($values, 0, -2);

        $this->query = "UPDATE {$table} SET {$keys} = {$values}";

        return $this;
    }

    public function delete(string $table, array $data = []): int
    {
        $this->query = "DELETE FROM {$table} WHERE ";

        $keys = '';
        $values = '';
        foreach ($data as $key => $param) {

            if ($param instanceof Query === true) {
                $keys = $key . ',';
                $values = "({$param->finalQuery}) AND ";

                $this->query .= $key . ' = ' . $values;
                continue;
            }

            $this->prepareParamsFromQuery[] = [$key => $param];

            $keys .= $key . ',';
            $values = strripos($param, '=') ? "($param)" . ' AND ' : '? AND ';

            $this->query .= " {$key} = {$values}";
        }

        $keys = mb_substr($keys, 0, -1);
        $values = mb_substr($values, 0, -2);
        $this->query = mb_substr($this->query, 0, -5);

        return $this->execute();
    }

    public function innerJoin($table, $condition, $type = 'INNER'): self
    {
        $this->joins[] = "{$type} JOIN {$table} ON {$condition}";
        return $this;
    }


    /**
     * Для написания любого запроса без вспомогательных методов
     * @return array|false
     */
    public function execRaw(string $query): array|false
    {
        $this->query = $query;
        $statement = $this->pdo->prepare($this->query);
        $statement->execute();
        $this->reset();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Аналог защиты от SQL инъекций в PDO
     * @param string $query
     * @param array $params
     *
     * @return string
     */
    public function interpolateQuery(string $query, array $params)
    {
        $keys = [];
        $values = [];

        foreach ($params as $key => $value) {

            if (is_string($key) === true) {
                $keys[] = '/:' . $key . '/';
            }

            if (is_string($key) === false)
            {
                $keys[] = '/[?]/';
            }

            if (is_numeric($value) === true) {
                $values[] = $value;
            }

            if (is_numeric($value) === false && strpos($value, '=') === false) {
                $values[] = "'" . $value . "'";
            }

            if (is_numeric($value) === false && strpos($value, '=') !== false) {
                $values[] = $value;
            }
        }

        return preg_replace($keys, $values, $query, 1);
    }

}