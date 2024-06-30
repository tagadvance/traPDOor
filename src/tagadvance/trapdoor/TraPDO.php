<?php

namespace tagadvance\trapdoor;

class TraPDO extends \PDO {

    function __construct($dsn, $username = null, string $password = null, array $driver_options = null) {
        parent::__construct($dsn, $username, $password, $driver_options);
        
        if (! $this->isPersistent()) {
            // http://www.php.net/manual/en/pdo.setattribute.php
            $classname = 'tagadvance\trapdoor\NonpersistentTraPDOStatement';
            $value = [
                    $classname,
                    $constructor_args = [
                            $this
                    ]
            ];
            $this->setAttribute(\PDO::ATTR_STATEMENT_CLASS, $value);
        }
    }

    function prepare($statement, $driver_options = null): \PDOStatement|false {
		$args = func_get_args();
        $statement = parent::prepare($args[0], $args[1] ?? []);
        if ($statement !== false && $this->isPersistent()) {
            return new PersistentTrapPDOStatement($statement);
        }
        return $statement;
    }

    // TODO: magic is* methods
    function isPersistent() {
        return $this->getAttribute(\PDO::ATTR_PERSISTENT);
    }

}
