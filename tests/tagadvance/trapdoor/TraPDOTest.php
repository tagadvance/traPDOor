<?php

namespace tagadvance\trapdoor;

use PHPUnit\Framework\TestCase;

class TraPDOTest extends TestCase {
	
	function testConstructor() {
		$dsn = 'sqlite::memory:';
		$pdo = new TraPDO($dsn);
		$this->assertNotNull($pdo);
	}
	
	function testGetPreparedQueryStringUsingQuestionMarkPlaceholders() {
		$dsn = 'sqlite::memory:';
		$pdo = new TraPDO($dsn);
		$pdo->exec( 'CREATE TABLE foo (a TEXT, b TEXT, c TEXT);' );
		
		$expected = 'SELECT * FROM foo WHERE a = "one" AND b = 2 AND c = "three"';
		
		$sql = 'SELECT * FROM foo WHERE a = ? AND b = ? AND c = ?';
		$statement = $pdo->prepare($sql);
		$statement->bindValue(3, 'three');
		$statement->bindValue(2, 2);
		$statement->bindValue(1, 'one');
		$actual = $statement->getPreparedQueryString();
		
		$this->assertEquals($expected, $actual);
	}
	
	function testGetPreparedQueryStringUsingParameterNames() {
		$dsn = 'sqlite::memory:';
		$pdo = new TraPDO($dsn);
		$pdo->exec( 'CREATE TABLE foo (a TEXT, b TEXT, c TEXT);' );
		
		$expected = 'SELECT * FROM foo WHERE a = "one" AND b = 2 AND c = "three"';
		
		$sql = 'SELECT * FROM foo WHERE a = :a AND b = :b AND c = :c';
		$statement = $pdo->prepare($sql);
		$statement->bindValue(':a', 'one');
		$statement->bindValue(':b', 2);
		$statement->bindValue(':c', 'three');
		$actual = $statement->getPreparedQueryString();
		
		$this->assertEquals($expected, $actual);
	}
	
}