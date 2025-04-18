<?php

namespace tagadvance\trapdoor;

use PDOStatement;

/**
 * Can NOT be used with persistent PDO instances.
 */
class NonpersistentTraPDOStatement extends PDOStatement implements TraPDOStatement
{

	private array $bindings = [];

	protected function __construct()
	{
	}

	function bindColumn($column, &$var, $type = null, $maxLength = null, $driverOptions = null): bool
	{
		$this->bindings[$column] = $var;

		return parent::bindColumn($column, $var, $type, $maxLength, $driverOptions);
	}

	function bindParam($param, &$var, $type = null, $maxLength = null, $driverOptions = null): bool
	{
		$this->bindings[$param] = $var;

		return parent::bindParam($param, $var, $type, $maxLength, $driverOptions);
	}

	function bindValue($param, $value, $type = null): bool
	{
		$this->bindings[$param] = $value;

		return parent::bindValue($param, $value, $type);
	}

	function getPreparedQueryString(): string
	{
		return QueryFormatter::prepareQueryString($this->queryString, $this->bindings);
	}

	function __destruct()
	{
		unset($this->bindings);
	}

}
