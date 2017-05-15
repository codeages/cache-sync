<?php

use Codeages\CacheSync\Cache\RedisCache;
use Codeages\CacheSync\Synchronizer;
use Codeages\CacheSync\Database\DBALDatabase;
use Codeages\CacheSync\Database\PDODatabase;
use PHPUnit\Framework\TestCase;

class SynchronizerTest extends TestCase
{
    /**
     * @dataProvider databaseProvider
     */
    public function testPush($conn, $db, $cache)
    {
        $cache->flush();
        $conn->beginTransaction();

        $sync = new Synchronizer($db, $cache, $_ENV['DB_TABLE'], array());
        $pushed = $sync->push('set', 'test key', 'test value');

        $conn->rollBack();
    }

    public function databaseProvider()
    {
        $dbal = Doctrine\DBAL\DriverManager::getConnection(array(
            'dbname' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'host' => $_ENV['DB_HOST'],
            'driver' => 'pdo_mysql',
            'charset' => 'utf8',
        ));

        $pdo = new PDO(
            sprintf('mysql:dbname=%s;host=%s', $_ENV['DB_NAME'], $_ENV['DB_HOST']),
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD']
        );

        $redis = new \Redis();
        $redis->connect($_ENV['REDIS_HOST'], $_ENV['REDIS_PORT']);

        return array(
            array($dbal, new DBALDatabase($dbal), new RedisCache($redis)),
            array($pdo, new PDODatabase($pdo), new RedisCache($redis)),
        );
    }
}