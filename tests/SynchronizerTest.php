<?php
namespace Codeages\CacheSync;

class SynchronizerTest extends BaseTestCase
{
    /**
     * @dataProvider provider
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
     * @dataProvider provider
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
     * @dataProvider provider
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
}