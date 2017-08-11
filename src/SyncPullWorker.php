<?php

namespace Codeages\CacheSync;

use Codeages\Plumber\IWorker;
use Pimple\Container;

class SyncPullWorker implements IWorker
{
    const DELAY = 1;

    const SYNC_PER_LIMIT = 100;

    protected $container;

    public function setContainer(Container $container = null)
    {
        $this->container = $container;
    }

    public function execute($job)
    {
        $synced = $this->container['cache_sync_puller']->pull(self::SYNC_PER_LIMIT);
        if (!$synced) {
            return ['code' => IWorker::RETRY, 'delay' => 2];
        } else {
            return ['code' => IWorker::RETRY, 'delay' => 1];
        }
    }
}
