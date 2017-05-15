<?php
namespace Codeages\CacheSync;

use PHPUnit\Framework\TestCase;
use Codeages\CacheSync\Cache\RedisCache;
use Codeages\CacheSync\Synchronizer;
use Codeages\CacheSync\Database\DBALDatabase;
use Codeages\CacheSync\Database\PDODatabase;

class BaseTestCase extends TestCase
{
    /**
     * @dataProvider provider
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

    public function provider()
    {
        $dbal = \Doctrine\DBAL\DriverManager::getConnection(array(
            'dbname' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'host' => $_ENV['DB_HOST'],
            'driver' => 'pdo_mysql',
            'charset' => 'utf8',
        ));

        $pdo = new \PDO(
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