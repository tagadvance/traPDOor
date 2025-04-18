<?php

namespace tagadvance\trapdoor;

use PDOStatement;

class PersistentTrapPDOStatement extends PDOStatement implements TraPDOStatement
{

	protected PDOStatement $statement;

	private array $bindings = [];

	function __construct(PDOStatement $statement)
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
			$name
		];

		return call_user_func_array($callback, $arguments);
	}

	function bindColumn($column, &$var, $type = null, $maxLength = null, $driverOptions = null): bool
	{
		$this->bindings[$column] = $var;

		return $this->statement->bindColumn($column, $var, $type, $maxLength, $driverOptions);
	}

	function bindParam($param, &$var, $type = null, $maxLength = null, $driverOptions = null): bool
	{
		$this->bindings[$param] = $var;

		return $this->statement->bindParam($param, $var, $type, $maxLength, $driverOptions);
	}

	function bindValue($param, $value, $type = null): bool
	{
		$this->bindings[$param] = $value;

		return $this->statement->bindValue($param, $value, $type);
	}

	function getPreparedQueryString(): string
	{
		return QueryFormatter::prepareQueryString($this->queryString, $this->bindings);
	}

	function __destruct()
	{
		unset($this->statement, $this->bindings);
	}

}
