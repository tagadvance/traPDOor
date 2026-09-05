<?php

namespace tagadvance\trapdoor;

/**
 * A prepared statement that can render itself with its bound values for debug-level
 * logging.
 */
interface TraPDOStatement
{
    /**
     * Debug output only: the rendered query is not valid SQL and must never be executed,
     * because values are not escaped the way the driver would escape them. See
     * QueryFormatter for the ways the rendering is lossy.
     */
    public function getPreparedQueryString(): string;

}
