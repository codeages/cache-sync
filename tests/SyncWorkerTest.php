<?php
namespace Codeages\CacheSync;

class SyncWorkerTest extends BaseTestCase
{
    /**
     * @dataProvider provider
     */
    public function testExecute($conn, $db, $cache, $sync)
    {
        $executed = $this->execute($conn, $db, $cache, $sync);

        $this->assertArrayHasKey('code', $executed);
        $this->assertArrayHasKey('delay', $executed);
        $this->assertEquals(\Codeages\Plumber\IWorker::RETRY, $executed['code']);
    }

    protected function execute($conn, $db, $cache, $sync)
    {
        $container = new \Pimple\Container();
        $container['cache_sync'] = $sync;

        $worker = new SyncWorker();
        $worker->setContainer($container);

        $job = ['id' => 1, 'body' => []];
        $executed = $worker->execute($job);

        return $executed;
    }
}
