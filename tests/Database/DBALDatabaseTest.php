<?php

namespace Codeages\CacheSync\Database;

use PHPUnit\Framework\TestCase;
use Codeages\CacheSync\Database;

class DBALDatabaseTest extends TestCase
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    protected $dbal;

    public function setUp()
    {
        $this->dbal = $dbal = \Doctrine\DBAL\DriverManager::getConnection(array(
            'dbname' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'host' => $_ENV['DB_HOST'],
            'driver' => 'pdo_mysql',
            'charset' => 'utf8',
        ));

        $this->dbal->beginTransaction();
    }

    public function tearDown()
    {
        $this->dbal->rollBack();
    }

    public function testExecute()
    {
        $db = new DBALDatabase($this->dbal);

        $inserted = $this->insertFakeRow($db);

        $this->assertEquals(1, $inserted);
    }

    public function testQuery()
    {
        $db = new DBALDatabase($this->dbal);
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
