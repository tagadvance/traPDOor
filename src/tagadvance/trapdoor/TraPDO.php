<?php

namespace tagadvance\trapdoor;

use PDO;
use PDOStatement;

/**
 * A PDO whose prepared statements can render themselves with their bound values for
 * debug-level logging. query() is not overridden, so it yields a
 * NonpersistentTraPDOStatement on a non-persistent connection but a plain PDOStatement,
 * with no getPreparedQueryString(), on a persistent one.
 */
class TraPDO extends PDO
{
    /**
     * The statement class is installed only on a non-persistent connection, because PDO
     * rejects PDO::ATTR_STATEMENT_CLASS on a persistent instance; prepare() wraps instead.
     *
     * @throws \PDOException when the connection cannot be established
     */
    public function __construct($dsn, $username = null, ?string $password = null, ?array $driver_options = null)
    {
        parent::__construct($dsn, $username, $password, $driver_options);

        if (!$this->isPersistent()) {
            // http://www.php.net/manual/en/pdo.setattribute.php
            $classname = 'tagadvance\trapdoor\NonpersistentTraPDOStatement';
            $value = [
                $classname,
                [
                    $this,
                ],
            ];
            $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, $value);
        }
    }

    /**
     * Returns a NonpersistentTraPDOStatement on a non-persistent connection; on a
     * persistent one it returns a PersistentTrapPDOStatement, on which nothing but
     * getPreparedQueryString() works.
     *
     * @throws \PDOException when the statement cannot be prepared and the error mode is
     *         PDO::ERRMODE_EXCEPTION, which is the default
     */
    public function prepare($query, $options = null): PDOStatement|false
    {
        $args = func_get_args();
        $query = parent::prepare($args[0], $args[1] ?? []);
        if ($query !== false && $this->isPersistent()) {
            return new PersistentTrapPDOStatement($query);
        }

        return $query;
    }

    // TODO: magic is* methods
    public function isPersistent()
    {
        return $this->getAttribute(PDO::ATTR_PERSISTENT);
    }

}
