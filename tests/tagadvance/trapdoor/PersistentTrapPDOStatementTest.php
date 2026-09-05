<?php

declare(strict_types=1);

namespace tagadvance\trapdoor;

use PDO;
use PHPUnit\Framework\TestCase;

class PersistentTrapPDOStatementTest extends TestCase
{
    private PDO $pdo;

    public function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->exec('CREATE TABLE foo (a TEXT, b TEXT, c TEXT);');
    }

    public function testGetPreparedQueryStringUsingParameterNames()
    {
        $expected = 'SELECT * FROM foo WHERE a = "one" AND b = 2 AND c = "three"';

        $sql = 'SELECT * FROM foo WHERE a = :a AND b = :b AND c = :c';
        $statement = new PersistentTrapPDOStatement($this->pdo->prepare($sql));
        $statement->bindValue(':a', 'one');
        $statement->bindValue(':b', 2);
        $statement->bindValue(':c', 'three');
        $actual = $statement->getPreparedQueryString();

        $this->assertEquals($expected, $actual);
    }

}
