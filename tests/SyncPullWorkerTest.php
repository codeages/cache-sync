<?php
namespace Codeages\CacheSync;

class SyncPullWorkerTest extends BaseTestCase
{
    /**
     * @dataProvider provider
     */
    public function testExecute($conn, $db, $cache, $pusher, $puller)
    {
        $executed = $this->execute($conn, $db, $cache, $pusher, $puller);

        $this->assertArrayHasKey('code', $executed);
        $this->assertArrayHasKey('delay', $executed);
        $this->assertEquals(\Codeages\Plumber\IWorker::RETRY, $executed['code']);
    }

    protected function execute($conn, $db, $cache, $pusher, $puller)
    {
        $container = new \Pimple\Container();
        $container['cache_sync_puller'] = $puller;

        $worker = new SyncPullWorker();
        $worker->setContainer($container);

        $job = ['id' => 1, 'body' => []];
        $executed = $worker->execute($job);

        return $executed;
    }
}
