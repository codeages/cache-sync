<?php

namespace Codeages\CacheSync;

use Codeages\Plumber\IWorker;
use Pimple\Container;

class SyncWorker implements IWorker
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
        $this->container['cache_sync']->sync(self::SYNC_PER_LIMIT);

        return ['code' => IWorker::RETRY, 'delay' => self::DELAY];
    }
}
