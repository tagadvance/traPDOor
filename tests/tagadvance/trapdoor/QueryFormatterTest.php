<?php

namespace tagadvance\trapdoor;

use PHPUnit\Framework\TestCase;

class QueryFormatterTest extends TestCase {
	
	function testPrepareQueryStringUsingQuestionMarkPlaceholders() {
		$expected = 'SELECT * FROM foo.bar WHERE a = 1 AND b = "two" AND c = 3';
		
		$sql = 'SELECT * FROM foo.bar WHERE a = ? AND b = ? AND c = ?';
		$bindings = [ 
				1,
				'two',
				3 
		];
		$actual = QueryFormatter::prepareQueryString ( $sql, $bindings );
		
		$this->assertEquals ( $expected, $actual );
	}
	
	function testPrepareQueryStringUsingParameterNames() {
		$expected = 'SELECT * FROM foo.bar WHERE a = 1 AND b = "two" AND c = 3';
		
		$sql = 'SELECT * FROM foo.bar WHERE a = :a AND b = :b AND c = :c';
		$bindings = [ 
				':a' => 1,
				':b' => 'two',
				':c' => 3 
		];
		$actual = QueryFormatter::prepareQueryString ( $sql, $bindings );
		
		$this->assertEquals ( $expected, $actual );
	}
	
}