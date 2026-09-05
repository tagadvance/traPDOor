<?php

namespace tagadvance\trapdoor;

/**
 * Renders a query with its bound values substituted, for debug-level logging only. The
 * output is not valid SQL and must never be executed.
 */
class QueryFormatter
{
    private function __construct() {}

    /**
     * Value rendering is deliberately lossy: null and false both render as "", true as
     * "1", a numeric string such as '007' renders unquoted, and an embedded " is not
     * escaped. Placeholders are substituted wherever they appear, including inside SQL
     * string literals and comments, so `WHERE a = 'wh?t' AND b = ?` consumes the
     * literal's ? as the first placeholder.
     *
     * @param array $bindings positional values keyed by 1-based ordinal, named values
     *        keyed by parameter name with or without its leading colon
     */
    public static function prepareQueryString(string $queryString, array $bindings): string
    {
        $positional = [];
        $named = [];
        foreach ($bindings as $parameter => $variable) {
            $value = is_numeric($variable) ? (string) $variable : "\"$variable\"";
            if (is_int($parameter)) {
                $positional[$parameter] = $value;
            } else {
                // PDO accepts a named parameter with or without its leading colon.
                $named[ltrim($parameter, ':')] = $value;
            }
        }

        $ordinal = 0;
        $substitute = function (array $matches) use ($positional, $named, &$ordinal): string {
            $placeholder = $matches[0];
            if ($placeholder === '?') {
                $ordinal++;

                return $positional[$ordinal] ?? $placeholder;
            }

            return $named[substr($placeholder, 1)] ?? $placeholder;
        };

        // One pass, so a substituted value is never rescanned and :a is never
        // matched inside :ab.
        return preg_replace_callback('/\?|:\w+/', $substitute, $queryString);
    }

}
