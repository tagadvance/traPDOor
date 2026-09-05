<?php

namespace tagadvance\trapdoor;

use PHPUnit\Framework\TestCase;

class QueryFormatterTest extends TestCase
{
    public function testPrepareQueryStringUsingQuestionMarkPlaceholders()
    {
        $expected = 'SELECT * FROM foo.bar WHERE a = 1 AND b = "two" AND c = 3';

        $sql = 'SELECT * FROM foo.bar WHERE a = ? AND b = ? AND c = ?';
        $bindings = [
            3 => 3,
            2 => 'two',
            1 => 1,
        ];
        $actual = QueryFormatter::prepareQueryString($sql, $bindings);

        $this->assertEquals($expected, $actual);
    }

    public function testPrepareQueryStringUsingParameterNames()
    {
        $expected = 'SELECT * FROM foo.bar WHERE a = 1 AND b = "two" AND c = 3';

        $sql = 'SELECT * FROM foo.bar WHERE a = :a AND b = :b AND c = :c';
        $bindings = [
            ':a' => 1,
            ':b' => 'two',
            ':c' => 3,
        ];
        $actual = QueryFormatter::prepareQueryString($sql, $bindings);

        $this->assertEquals($expected, $actual);
    }


    public function testPrepareQueryStringDoesNotRescanSubstitutedValues()
    {
        $expected = 'SELECT * FROM foo.bar WHERE a = "wh?t" AND b = "two"';

        $sql = 'SELECT * FROM foo.bar WHERE a = ? AND b = ?';
        $bindings = [
            1 => 'wh?t',
            2 => 'two',
        ];
        $actual = QueryFormatter::prepareQueryString($sql, $bindings);

        $this->assertEquals($expected, $actual);
    }

    public function testPrepareQueryStringDoesNotSubstituteInsideALongerParameterName()
    {
        $expected = 'SELECT * FROM foo.bar WHERE a = 1 AND ab = 2';

        $sql = 'SELECT * FROM foo.bar WHERE a = :a AND ab = :ab';
        $bindings = [
            ':a' => 1,
            ':ab' => 2,
        ];
        $actual = QueryFormatter::prepareQueryString($sql, $bindings);

        $this->assertEquals($expected, $actual);
    }

    public function testPrepareQueryStringWithNoncontiguousPositionalBindings()
    {
        $expected = 'SELECT * FROM foo.bar WHERE a = ? AND b = "two" LIMIT 2';

        $sql = 'SELECT * FROM foo.bar WHERE a = ? AND b = ? LIMIT 2';
        $bindings = [
            2 => 'two',
        ];
        $actual = QueryFormatter::prepareQueryString($sql, $bindings);

        $this->assertEquals($expected, $actual);
    }

    public function testPrepareQueryStringUsingParameterNamesWithoutALeadingColon()
    {
        $expected = 'SELECT * FROM foo.bar WHERE a = "one"';

        $sql = 'SELECT * FROM foo.bar WHERE a = :a';
        $bindings = [
            'a' => 'one',
        ];
        $actual = QueryFormatter::prepareQueryString($sql, $bindings);

        $this->assertEquals($expected, $actual);
    }

}
