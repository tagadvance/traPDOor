<?php

namespace tagadvance\trapdoor;

use PDO;
use PDOStatement;

/**
 * Effectively dead: this class both extends and wraps PDOStatement, so every inherited
 * method is already defined, __call() never forwards, and execute() fails with "object is
 * uninitialized". Only getPreparedQueryString() works, and TraPDO::prepare() returns this
 * class for every persistent connection.
 */
class PersistentTrapPDOStatement extends PDOStatement implements TraPDOStatement
{
    protected PDOStatement $statement;

    private array $bindings = [];

    public function __construct(PDOStatement $statement)
    {
        $this->statement = $statement;
    }

    /**
     * Only reached for properties PDOStatement does not declare, so $queryString resolves
     * to this object's own uninitialized copy instead of the wrapped statement's.
     */
    public function __get($name)
    {
        return $this->statement->$name;
    }

    /**
     * Never fires: extending PDOStatement already defines every method this would
     * forward, which is why nothing but getPreparedQueryString() works on this class.
     */
    public function __call($name, $arguments)
    {
        $callback = [
            $this->statement,
            $name,
        ];

        return call_user_func_array($callback, $arguments);
    }

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

        return $this->statement->bindColumn($column, $var, $type, $maxLength, $driverOptions);
    }

    /**
     * The variable is held by reference, so getPreparedQueryString() renders whatever it
     * holds when the string is rendered rather than what it held when it was bound.
     */
    public function bindParam($param, &$var, $type = PDO::PARAM_STR, $maxLength = 0, $driverOptions = null): bool
    {
        $this->bindings[$param] = &$var;

        return $this->statement->bindParam($param, $var, $type, $maxLength, $driverOptions);
    }

    /**
     * Records a snapshot of the value; on a parameter previously bound with bindParam()
     * it replaces the reference rather than writing through to the caller's variable.
     */
    public function bindValue($param, $value, $type = PDO::PARAM_STR): bool
    {
        unset($this->bindings[$param]);
        $this->bindings[$param] = $value;

        return $this->statement->bindValue($param, $value, $type);
    }

    /**
     * The one method on this class that works. Debug output only: the rendered query is
     * not valid SQL and must never be executed.
     */
    public function getPreparedQueryString(): string
    {
        // $queryString is declared by PDOStatement, so __get() never fires for
        // it and this class's own copy is never initialized.
        return QueryFormatter::prepareQueryString($this->statement->queryString, $this->bindings);
    }

}
