<?php

namespace tagadvance\trapdoor;

class QueryFormatter
{
    private function __construct() {}

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
