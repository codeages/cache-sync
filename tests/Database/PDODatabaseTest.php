<?php

namespace Codeages\CacheSync\Database;

use PHPUnit\Framework\TestCase;
use Codeages\CacheSync\Database;

class PDODatabaseTest extends TestCase
{
    /**
     * @var PDO
     */
    protected $pdo;

    public function setUp()
    {
        $dsn = sprintf('mysql:dbname=%s;host=%s', $_ENV['DB_NAME'], $_ENV['DB_HOST']);
        $this->pdo = $pdo = new \PDO($dsn, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
        $pdo->beginTransaction();
    }

    public function tearDown()
    {
        $this->pdo->rollBack();
    }

    public function testExecute()
    {
        $db = new PDODatabase($this->pdo);

        $inserted = $this->insertFakeRow($db);

        $this->assertEquals(1, $inserted);
    }

    public function testQuery()
    {
        $db = new PDODatabase($this->pdo);
        $this->insertFakeRow($db);

        $result = $db->query("SELECT * FROM {$_ENV['DB_TABLE']}");
        $this->assertEquals(1, count($result));
        $this->assertEquals('test key', $result[0]['k']);
        $this->assertEquals('test value', $result[0]['v']);
    }

    protected function insertFakeRow(Database $db)
    {
        return $db->execute("INSERT INTO {$_ENV['DB_TABLE']} (`op`, `k`, `v`, `t`) VALUES (:op, :k, :v, :t)", array(
            'op' => 'set',
            'k' => 'test key',
            'v' => 'test value',
            't' => time(),
        ));
    }
}
