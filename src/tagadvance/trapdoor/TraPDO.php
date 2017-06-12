<?php

namespace tagadvance\trapdoor;

class TraPDO extends \PDO {
	
	function __construct($dsn, $username = '', $password = '', $driver_options = array()) {
		parent::__construct ( $dsn, $username, $password, $driver_options );
		
		if (! $this->isPersistent ()) {
			// http://www.php.net/manual/en/pdo.setattribute.php
			$classname = 'tagadvance\trapdoor\NonpersistentTraPDOStatement';
			$constructor_args = array (
					$this 
			);
			$value = array (
					$classname,
					$constructor_args 
			);
			$this->setAttribute ( \PDO::ATTR_STATEMENT_CLASS, $value );
		}
	}
	
	function prepare($statement, $driver_options = array()) {
		$statement = parent::prepare ( $statement, $driver_options );
		if ($statement !== false && $this->isPersistent ()) {
			return new PDOStatementDelegate ( $statement );
		}
		return $statement;
	}
	
	// TODO: magic is* methods
	function isPersistent() {
		return $this->getAttribute ( \PDO::ATTR_PERSISTENT );
	}
	
}