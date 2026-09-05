<?php

namespace tagadvance\trapdoor;

use PDO;
use PDOStatement;

/**
 * The statement class TraPDO installs on a non-persistent connection; PDO rejects
 * PDO::ATTR_STATEMENT_CLASS on a persistent instance, which is why this cannot serve one.
 */
class NonpersistentTraPDOStatement extends PDOStatement implements TraPDOStatement
{
    private array $bindings = [];

    /**
     * PDO instantiates this class itself through PDO::ATTR_STATEMENT_CLASS and rejects a
     * statement class with a public constructor, so instances cannot be created directly.
     */
    protected function __construct() {}

    /**
     * A result column contributes nothing to the query's placeholders, but the binding is
     * recorded anyway, so bindColumn(1, $col) injects a bogus first positional value into
     * getPreparedQueryString().
     */
    public function bindColumn($column, &$var, $type = null, $maxLength = null, $driverOptions = null): bool
    {
        // Break any reference left in this slot by bindParam().
        unset($this->bindings[$column]);
        $this->bindings[$column] = $var;

        return parent::bindColumn($column, $var, $type, $maxLength, $driverOptions);
    }

    /**
     * The variable is held by reference, so getPreparedQueryString() renders whatever it
     * holds when the string is rendered rather than what it held when it was bound.
     */
    public function bindParam($param, &$var, $type = PDO::PARAM_STR, $maxLength = 0, $driverOptions = null): bool
    {
        $this->bindings[$param] = &$var;

        return parent::bindParam($param, $var, $type, $maxLength, $driverOptions);
    }

    /**
     * Records a snapshot of the value; on a parameter previously bound with bindParam()
     * it replaces the reference rather than writing through to the caller's variable.
     */
    public function bindValue($param, $value, $type = PDO::PARAM_STR): bool
    {
        unset($this->bindings[$param]);
        $this->bindings[$param] = $value;

        return parent::bindValue($param, $value, $type);
    }

    /**
     * Debug output only: the rendered query is not valid SQL and must never be executed.
     * See QueryFormatter for the ways the rendering is lossy.
     */
    public function getPreparedQueryString(): string
    {
        return QueryFormatter::prepareQueryString($this->queryString, $this->bindings);
    }

}
