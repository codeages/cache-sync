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
        $this->cache = new RedisCache($_ENV['REDIS_HOST'], $_ENV['REDIS_PORT']);
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

    public function testReconnect()
    {
        $this->cache->instance()->close();
        $this->cache->reconnect();

        $this->cache->set('test key', 'test value');
        $value = $this->cache->get('test key');
        $this->assertEquals('test value', $value);
    }
}
