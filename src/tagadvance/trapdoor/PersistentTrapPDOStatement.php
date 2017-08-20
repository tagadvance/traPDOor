<?php

namespace tagadvance\trapdoor;

class PersistentTrapPDOStatement implements TraPDOStatement {

    protected $statement;

    private $bindings = [];

    function __construct(\PDOStatement $statement) {
        $this->statement = $statement;
    }

    public function __get($name) {
        return $this->statement->$name;
    }

    public function __call($name, $arguments) {
        $callback = [
                $this->statement,
                $name
        ];
        return call_user_func_array($callback, $arguments);
    }

    function bindColumn($column, &$parameter, $type = null, $length = null, $driver_options = null) {
        $this->bindings[$column] = $parameter;
        return $statement->bindColumn($column, $parameter, $type, $length, $driver_options);
    }

    function bindParam($parameter, &$variable, $data_type = null, $length = null, $driver_options = null) {
        $this->bindings[$parameter] = $variable;
        return $statement->bindParam($parameter, $variable, $data_type, $length, $driver_options);
    }

    function bindValue($parameter, $variable, $data_type = null) {
        $this->bindings[$parameter] = $variable;
        return $statement->bindValue($parameter, $variable, $data_type);
    }

    function getPreparedQueryString(): string {
        return QueryFormatter::prepareQueryString($this->queryString, $this->bindings);
    }

    function __destruct() {
        unset($this->statement, $this->bindings);
    }

}