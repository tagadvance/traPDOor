<?php

namespace tagadvance\trapdoor;

class QueryFormatter {
	
	private function __construct() {
	}
	
	static function prepareQueryString(string $queryString, array $bindings): string {
		foreach ( $bindings as $parameter => $variable ) {
			$value = is_numeric ( $variable ) ? $variable : "\"$variable\"";
			if (is_numeric ( $parameter )) {
				$needle = '?';
				$length = strlen ( $needle );
				
				$position = strpos ( $queryString, $needle );
				if ($position !== false) {
					$queryString = substr_replace ( $queryString, $value, $position, $length);
				}
			} else {
				$queryString = str_replace ( $parameter, $value, $queryString );
			}
		}
		return $queryString;
	}
	
}