<?php

namespace tagadvance\trapdoor;

use PDO;
use PDOStatement;

class PersistentTrapPDOStatement extends PDOStatement implements TraPDOStatement
{
    protected PDOStatement $statement;

    private array $bindings = [];

    public function __construct(PDOStatement $statement)
    {
        $this->statement = $statement;
    }

    public function __get($name)
    {
        return $this->statement->$name;
    }

    public function __call($name, $arguments)
    {
        $callback = [
            $this->statement,
            $name,
        ];

        return call_user_func_array($callback, $arguments);
    }

    public function bindColumn($column, &$var, $type = null, $maxLength = null, $driverOptions = null): bool
    {
        $this->bindings[$column] = $var;

        return $this->statement->bindColumn($column, $var, $type, $maxLength, $driverOptions);
    }

    public function bindParam($param, &$var, $type = PDO::PARAM_STR, $maxLength = 0, $driverOptions = null): bool
    {
        $this->bindings[$param] = $var;

        return $this->statement->bindParam($param, $var, $type, $maxLength, $driverOptions);
    }

    public function bindValue($param, $value, $type = PDO::PARAM_STR): bool
    {
        $this->bindings[$param] = $value;

        return $this->statement->bindValue($param, $value, $type);
    }

    public function getPreparedQueryString(): string
    {
        // $queryString is declared by PDOStatement, so __get() never fires for
        // it and this class's own copy is never initialized.
        return QueryFormatter::prepareQueryString($this->statement->queryString, $this->bindings);
    }

}
