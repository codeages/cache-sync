<?php
namespace Codeages\CacheSync;

use Codeages\CacheSync\Synchronizer;
use PHPUnit\Framework\TestCase;
use Codeages\CacheSync\SyncWorker;

class SyncWorkerTest extends TestCase
{
    public function testExecute()
    {
        $executed = $this->execute();

        $this->assertArrayHasKey('code', $executed);
        $this->assertArrayHasKey('delay', $executed);
        $this->assertEquals(\Codeages\Plumber\IWorker::RETRY, $executed['code']);
    }

    protected function execute()
    {
        $container = new \Pimple\Container();
//        $container['cache_sync'] = new Synchronizer();

        $worker = new SyncWorker();


        $job = ['id' => 1, 'body' => []];
        $executed = $worker->execute($job);

        return $executed;
    }

}