<?php

namespace tagadvance\trapdoor;

use PDO;
use PDOStatement;

/**
 * Can NOT be used with persistent PDO instances.
 */
class NonpersistentTraPDOStatement extends PDOStatement implements TraPDOStatement
{
    private array $bindings = [];

    protected function __construct() {}

    public function bindColumn($column, &$var, $type = null, $maxLength = null, $driverOptions = null): bool
    {
        $this->bindings[$column] = $var;

        return parent::bindColumn($column, $var, $type, $maxLength, $driverOptions);
    }

    public function bindParam($param, &$var, $type = PDO::PARAM_STR, $maxLength = 0, $driverOptions = null): bool
    {
        $this->bindings[$param] = $var;

        return parent::bindParam($param, $var, $type, $maxLength, $driverOptions);
    }

    public function bindValue($param, $value, $type = PDO::PARAM_STR): bool
    {
        $this->bindings[$param] = $value;

        return parent::bindValue($param, $value, $type);
    }

    public function getPreparedQueryString(): string
    {
        return QueryFormatter::prepareQueryString($this->queryString, $this->bindings);
    }

}
