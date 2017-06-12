<?php

namespace tagadvance\trapdoor;

class PersistentTrapPDOStatement implements TrapPDOStatement {
	
	protected $statement;
	private $bindings = [ ];
	
	function __construct(\PDOStatement $statement) {
		$this->statement = $statement;
	}
	
	public function __get($name) {
		return $this->statement->$name;
	}
	
	public function __call($name, $arguments) {
		$callback = array (
				$this->statement,
				$name 
		);
		return call_user_func_array ( $callback, $arguments );
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
		unset ( $this->statement, $this->bindings );
	}
	
}