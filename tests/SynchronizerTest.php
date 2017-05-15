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
    public function testPush_set($conn, $db, $cache)
    {
        $sync = $this->beforeTest($conn, $db, $cache);

        $pushed = $sync->push('set', 'test key 1', 'test value 1');
        $pushed = $sync->push('set', 'test key 2', 'test value 2');

        $synced = $sync->sync(100);
        $this->assertEquals(2, $synced);

        $value = $cache->get('test key 1');
        $this->assertEquals('test value 1', $value);

        $this->afterTest($conn, $db, $cache);
    }

    /**
     * @dataProvider databaseProvider
     */
    public function testPush_setOfHaveOldKey($conn, $db, $cache)
    {
        $sync = $this->beforeTest($conn, $db, $cache);

        $cache->set('test key 1', 'old test value 1');

        $pushed = $sync->push('set', 'test key 1', 'new test value 1');
        $pushed = $sync->push('set', 'test key 2', 'new test value 2');

        $synced = $sync->sync(100);
        $this->assertEquals(2, $synced);

        $value = $cache->get('test key 1');
        $this->assertEquals('new test value 1', $value);

        $value = $cache->get('test key 2');
        $this->assertEquals('new test value 2', $value);

        $this->afterTest($conn, $db, $cache);
    }

    /**
     * @dataProvider databaseProvider
     */
    public function testPush_del($conn, $db, $cache)
    {
        $sync = $this->beforeTest($conn, $db, $cache);

        $cache->set('test key 1', 'test value 1');
        $cache->set('test key 2', 'test value 2');

        $pushed = $sync->push('del', 'test key 1');

        $synced = $sync->sync(100);
        $this->assertEquals(1, $synced);

        $value = $cache->get('test key 1');
        $this->assertEquals(false, $value);

        $value = $cache->get('test key 2');
        $this->assertEquals('test value 2', $value);

        $this->afterTest($conn, $db, $cache);
    }

    /**
     * @dataProvider databaseProvider
     */
    public function testPush_delOfHaveNoKey($conn, $db, $cache)
    {
        $sync = $this->beforeTest($conn, $db, $cache);

        $pushed = $sync->push('del', 'test key 1');

        $synced = $sync->sync(100);
        $this->assertEquals(1, $synced);

        $value = $cache->get('test key 1');
        $this->assertEquals(false, $value);

        $this->afterTest($conn, $db, $cache);
    }

    protected function beforeTest($conn, $db, $cache)
    {
        $cache->flush();
        $conn->beginTransaction();

        $options = array(
            'cursor_file' => tempnam(sys_get_temp_dir(), 'cache_sync_cursor_'),
        );
        $sync = new Synchronizer($db, $cache, $_ENV['DB_TABLE'], $options);

        return $sync;
    }

    protected function afterTest($conn, $db, $cache)
    {
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