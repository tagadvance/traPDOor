<?php

namespace tagadvance\trapdoor;

/**
 * Can NOT be used with persistent PDO instances.
 */
class NonpersistentTraPDOStatement extends \PDOStatement implements TraPDOStatement {
	
	private $pdo;
	private $bindings = [ ];
	
	protected function __construct(\PDO $pdo) {
		$this->pdo = $pdo;
	}
	
	// TODO: other bind* methods
	function bindParam($parameter, &$variable, $data_type = null, $length = null, $driver_options = null) {
		$this->bindings [$parameter] = $variable;
		return parent::bindParam ( $parameter, $variable, $data_type, $length, $driver_options );
	}
	
	function bindValue($parameter, $variable, $data_type = null) {
		$this->bindings [$parameter] = $variable;
		return parent::bindValue ( $parameter, $variable, $data_type );
	}
	
	function getPreparedQueryString(): string {
		return QueryFormatter::prepareQueryString ( $this->queryString, $this->bindings );
	}
	
	function __destruct() {
		unset ( $this->pdo, $this->bindings );
	}
	
}