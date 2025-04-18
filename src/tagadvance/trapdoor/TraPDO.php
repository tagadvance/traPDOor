<?php

namespace tagadvance\trapdoor;

use PDO;
use PDOStatement;

class TraPDO extends PDO
{

	function __construct($dsn, $username = null, ?string $password = null, ?array $driver_options = null)
	{
		parent::__construct($dsn, $username, $password, $driver_options);

		if (!$this->isPersistent()) {
			// http://www.php.net/manual/en/pdo.setattribute.php
			$classname = 'tagadvance\trapdoor\NonpersistentTraPDOStatement';
			$value = [
				$classname,
				[
					$this
				]
			];
			$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, $value);
		}
	}

	function prepare($query, $options = null): PDOStatement|false
	{
		$args = func_get_args();
		$query = parent::prepare($args[0], $args[1] ?? []);
		if ($query !== false && $this->isPersistent()) {
			return new PersistentTrapPDOStatement($query);
		}

		return $query;
	}

	// TODO: magic is* methods
	function isPersistent()
	{
		return $this->getAttribute(PDO::ATTR_PERSISTENT);
	}

}
