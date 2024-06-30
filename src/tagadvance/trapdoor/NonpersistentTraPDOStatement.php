<?php

namespace tagadvance\trapdoor;

/**
 * Can NOT be used with persistent PDO instances.
 */
class NonpersistentTraPDOStatement extends \PDOStatement implements TraPDOStatement {

    private $pdo;

    private $bindings = [];

    protected function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    function bindColumn($column, &$parameter, $type = null, $length = null, $driver_options = null): bool {
        $this->bindings[$column] = $parameter;
        return parent::bindColumn($column, $parameter, $type, $length, $driver_options);
    }

    function bindParam($parameter, &$variable, $type = null, $length = null, $driver_options = null): bool {
        $this->bindings[$parameter] = $variable;
        return parent::bindParam($parameter, $variable, $type, $length, $driver_options);
    }

    function bindValue($parameter, $variable, $type = null): bool {
        $this->bindings[$parameter] = $variable;
        return parent::bindValue($parameter, $variable, $type);
    }

    function getPreparedQueryString(): string {
        return QueryFormatter::prepareQueryString($this->queryString, $this->bindings);
    }

    function __destruct() {
        unset($this->pdo, $this->bindings);
    }

}
