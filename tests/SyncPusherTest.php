<?php

namespace Codeages\CacheSync;

class SyncPusherTest extends BaseTestCase
{
    /**
     * @dataProvider provider
     */
    public function testPush_set($conn, $db, $cache, $pusher, $puller)
    {
        $cache->flush();
        $conn->beginTransaction();

        $pushed = $pusher->push('set', 'test key 1', 'test value 1');
        $pushed = $pusher->push('set', 'test key 2', array('hello' => 'world'));

        $pulled = $puller->pull(100);
        $this->assertEquals(2, $pulled);

        $value = $cache->get('test key 1');
        $this->assertEquals('test value 1', $value);

        $value = $cache->get('test key 2');
        $this->assertEquals(array('hello' => 'world'), $value);

        $conn->rollBack();
    }

    /**
     * @dataProvider provider
     */
    public function testPush_setOfHaveOldKey($conn, $db, $cache, $pusher, $puller)
    {
        $cache->flush();
        $conn->beginTransaction();

        $cache->set('test key 1', 'old test value 1');

        $pushed = $pusher->push('set', 'test key 1', 'new test value 1');
        $pushed = $pusher->push('set', 'test key 2', 'new test value 2');

        $pulled = $puller->pull(100);
        $this->assertEquals(2, $pulled);

        $value = $cache->get('test key 1');
        $this->assertEquals('new test value 1', $value);

        $value = $cache->get('test key 2');
        $this->assertEquals('new test value 2', $value);

        $conn->rollBack();
    }

    /**
     * @dataProvider provider
     */
    public function testPush_del($conn, $db, $cache, $pusher, $puller)
    {
        $cache->flush();
        $conn->beginTransaction();

        $cache->set('test key 1', 'test value 1');
        $cache->set('test key 2', 'test value 2');

        $pushed = $pusher->push('del', 'test key 1');

        $pulled = $puller->pull(100);
        $this->assertEquals(1, $pulled);

        $value = $cache->get('test key 1');
        $this->assertEquals(false, $value);

        $value = $cache->get('test key 2');
        $this->assertEquals('test value 2', $value);

        $conn->rollBack();
    }

    /**
     * @dataProvider provider
     */
    public function testPush_delOfHaveNoKey($conn, $db, $cache, $pusher, $puller)
    {
        $cache->flush();
        $conn->beginTransaction();

        $pushed = $pusher->push('del', 'test key 1');

        $pulled = $puller->pull(100);
        $this->assertEquals(1, $pulled);

        $value = $cache->get('test key 1');
        $this->assertEquals(false, $value);

        $conn->rollBack();
    }
}
