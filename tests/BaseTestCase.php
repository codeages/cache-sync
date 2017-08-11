<?php

namespace Codeages\CacheSync;

use PHPUnit\Framework\TestCase;
use Codeages\CacheSync\Cache\RedisCache;
use Codeages\CacheSync\Database\DBALDatabase;
use Codeages\CacheSync\Database\PDODatabase;

class BaseTestCase extends TestCase
{
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
        $dbalDatabase = new DBALDatabase($dbal);

        $pdo = new \PDO(
            sprintf('mysql:dbname=%s;host=%s', $_ENV['DB_NAME'], $_ENV['DB_HOST']),
            $_ENV['DB_USER'],
            $_ENV['DB_PASSWORD']
        );
        $pdoDatabase = new PDODatabase($pdo);

        $redisCache = new RedisCache($_ENV['REDIS_HOST'], $_ENV['REDIS_PORT']);

        $options = array(
            'table' => $_ENV['DB_TABLE'],
            'cursor_file' => tempnam(sys_get_temp_dir(), 'cache_sync_cursor_'),
        );

        return array(
            array(
                $dbal,
                $dbalDatabase,
                $redisCache,
                new SyncPusher($dbalDatabase, $options),
                new SyncPuller($dbalDatabase, $redisCache, $options),
            ),
            array(
                $pdo,
                $pdoDatabase,
                $redisCache,
                new SyncPusher($pdoDatabase, $options),
                new SyncPuller($pdoDatabase, $redisCache, $options),
            ),
        );
    }
}
