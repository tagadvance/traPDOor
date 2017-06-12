<?php

namespace tagadvance\trapdoor;

interface TraPDOStatement {
	
	/**
	 *
	 * @return string The human-readable sql with bound fields. Under no
	 *         circumstance should this string be run as the fields
	 *         have not been sanitized.
	 */
	function getPreparedQueryString();
	
}