<?php

namespace Codeages\CacheSync;

use Codeages\Plumber\IWorker;
use Pimple\Container;

class SyncWorker implements IWorker
{
    const DELAY = 1;

    const SYNC_PER_LIMIT = 100;

    public function setContainer(Container $container = null)
    {
        $this->container = $container;
    }

    public function execute($job)
    {
        return ['code' => IWorker::RETRY, 'delay' => self::DELAY];
    }
}
