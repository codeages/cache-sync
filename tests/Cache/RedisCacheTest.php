<?php
namespace Codeages\CacheSync\Cache;

use PHPUnit\Framework\TestCase;

class RedisCacheTest extends TestCase
{
    /**
     * @var \Codeages\CacheSync\Cache
     */
    protected $cache;

    public function setUp()
    {
        $redis = new \Redis();
        $redis->connect($_ENV['REDIS_HOST'], $_ENV['REDIS_PORT']);
        $this->cache = new RedisCache($redis);
        $this->cache->flush();
    }

    public function testSet()
    {
        $seted = $this->cache->set('test key', 'test value');
        $this->assertTrue(true, $seted);
    }

    public function testDel()
    {
        $this->cache->set('test key', 'test value');
        $deleted = $this->cache->del('test key');
        $this->assertEquals(1, $deleted);
    }
}
