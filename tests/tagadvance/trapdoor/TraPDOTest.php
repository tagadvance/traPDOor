<?php

namespace tagadvance\trapdoor;

use PDO;
use PHPUnit\Framework\TestCase;

class TraPDOTest extends TestCase
{
    private PDO $pdo;

    public function setUp(): void
    {
        $dsn = 'sqlite::memory:';
        $this->pdo = new TraPDO($dsn);
    }

    public function testConstructor()
    {
        $this->assertSame('sqlite', $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME));
    }

    public function testGetPreparedQueryStringUsingQuestionMarkPlaceholders()
    {
        $this->pdo->exec('CREATE TABLE foo (a TEXT, b TEXT, c TEXT);');

        $expected = 'SELECT * FROM foo WHERE a = "one" AND b = 2 AND c = "three"';

        $sql = 'SELECT * FROM foo WHERE a = ? AND b = ? AND c = ?';
        $statement = $this->pdo->prepare($sql);
        $this->assertInstanceOf(TraPDOStatement::class, $statement);
        $statement->bindValue(3, 'three');
        $statement->bindValue(2, 2);
        $statement->bindValue(1, 'one');
        $actual = $statement->getPreparedQueryString();

        $this->assertEquals($expected, $actual);
    }

    public function testGetPreparedQueryStringUsingParameterNames()
    {
        $this->pdo->exec('CREATE TABLE foo (a TEXT, b TEXT, c TEXT);');

        $expected = 'SELECT * FROM foo WHERE a = "one" AND b = 2 AND c = "three"';

        $sql = 'SELECT * FROM foo WHERE a = :a AND b = :b AND c = :c';
        $statement = $this->pdo->prepare($sql);
        $this->assertInstanceOf(TraPDOStatement::class, $statement);
        $statement->bindValue(':a', 'one');
        $statement->bindValue(':b', 2);
        $statement->bindValue(':c', 'three');
        $actual = $statement->getPreparedQueryString();

        $this->assertEquals($expected, $actual);
    }


    public function testGetPreparedQueryStringReflectsBindParamByReference()
    {
        $this->pdo->exec('CREATE TABLE foo (a TEXT);');

        $expected = 'SELECT * FROM foo WHERE a = "after"';

        $statement = $this->pdo->prepare('SELECT * FROM foo WHERE a = :a');
        $this->assertInstanceOf(TraPDOStatement::class, $statement);
        $value = 'before';
        $statement->bindParam(':a', $value);
        $value = 'after';
        $actual = $statement->getPreparedQueryString();

        $this->assertEquals($expected, $actual);
    }

    public function testBindValueDoesNotOverwriteAVariableBoundByBindParam()
    {
        $this->pdo->exec('CREATE TABLE foo (a TEXT);');

        $statement = $this->pdo->prepare('SELECT * FROM foo WHERE a = :a');
        $this->assertInstanceOf(TraPDOStatement::class, $statement);
        $value = 'bound';
        $statement->bindParam(':a', $value);
        $statement->bindValue(':a', 'other');

        $this->assertSame('bound', $value);
    }

}
