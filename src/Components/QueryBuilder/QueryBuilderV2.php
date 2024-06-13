<?php

namespace Craft\Components\QueryBuilder;

use Craft\Contracts\DataBaseConnectionInterface;
use Craft\Contracts\QueryBuilderInterface;
use \PDO;

class QueryBuilderV2 implements QueryBuilderInterface
{
    /**
     * @var PDO
     */
    protected $pdo;
    protected $table;
    protected $tableSelect;
    protected $tableInsert;
    protected $tableUpdate;
    protected $tableDelete;
    protected $query;
    protected $params = [];
    protected $joins = [];
    protected $wheres = [];
    protected $prepareParamsFromQuery = [];
    protected $prepareParamsFromDeleteQuery = [];
    protected $prepareQuery = [];


    public function __construct(private DataBaseConnectionInterface $db)
    {
        $this->pdo = $db->pdo;
    }



    public function getQuery(): string|int
    {

        $joinString = implode(' ', $this->joins);
        $whereString = implode(' ', array_slice($this->wheres, 0, -1)); // Удаляем последний коннектор
        $this->query .= " {$joinString} WHERE {$whereString}";
        $this->wheres = [];

        $this->prepareQuery[] = $this->query;
        $stmt = $this->pdo->prepare($this->query);

        return $this->query;
    }

    /**
     * @param $i
     * @return mixed
     */
    public function one(int $mode = PDO::FETCH_ASSOC): mixed
    {
        $i = 0;
        $joinString = implode(' ', $this->joins);
        $whereString = implode(' ', array_slice($this->wheres, 0, -1)); // Удаляем последний коннектор
        $this->query .= " {$joinString} WHERE {$whereString}";
        $this->wheres = [];


        $stmt = $this->pdo->prepare($this->query);

        foreach ($this->prepareParamsFromQuery as $key => $values) {

            foreach ($values as $param => $value) {

                if (strripos($value, '=') === false) {
                    $i += 1;
                    $stmt->bindValue($i, $value);
                }
                continue;
            }
        }
        $stmt->execute();
        $this->reset();
        return $stmt->fetch($mode);
    }

    /**
     * @param $i
     * @return mixed
     */
    public function all(int $mode = PDO::FETCH_ASSOC): mixed
    {
        $i = 0;
        $joinString = implode(' ', $this->joins);
        $whereString = implode(' ', array_slice($this->wheres, 0, -1)); // Удаляем последний коннектор

        $where = " WHERE ";
        if ($joinString === '' && $whereString === ''){
            $where = "";
        }
        $this->query .= $joinString . $where . $whereString;
        $this->wheres = [];

        $stmt = $this->pdo->prepare($this->query);

        foreach ($this->prepareParamsFromQuery as $key => $values) {

            foreach ($values as $param => $value) {

                if (strripos($value, '=') === false) {
                    $i += 1;
                    $stmt->bindValue($i, $value);
                }
                continue;
            }
        }
        $stmt->execute();
        $this->reset();

        return $stmt->fetchAll($mode);
    }


    /**
     * @param $i для цикла
     * @return array|false
     */
    protected function execute(): int
    {
        $i = 0;
        $joinString = implode(' ', $this->joins);
        $whereString = implode(' ', array_slice($this->wheres, 0, -1)); // Удаляем последний коннектор

        $where = " WHERE ";
        if ($joinString === '' && $whereString === ''){
            $where = "";
        }
        $this->query .= $joinString . $where . $whereString;

        $this->wheres = [];


        if (strripos($this->query, 'DELETE') !== false) {
            $this->query .= ' WHERE ';
            foreach ($this->prepareParamsFromDeleteQuery as $preparedQuery ) {
                $this->query .= $preparedQuery . ' AND ';
            }
            $this->query = substr($this->query, 0, -5);
        }

        $stmt = $this->pdo->prepare($this->query);
        foreach ($this->prepareParamsFromQuery as $key => $values) {
            foreach ($values as $param => $value) {
                if (strripos($value, '=') === false) {
                    $i += 1;
                    $stmt->bindValue($i, $value);
                }
                continue;
            }
        }

        $stmt->execute();
        $this->reset();

        return $this->pdo->lastInsertId();
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

}


