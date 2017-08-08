<?php

namespace Codeages\CacheSync\Cache;

use Codeages\CacheSync\Cache;

class RedisCache implements Cache
{
    public function __construct($redis)
    {
        $this->redis = $redis;
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }

    public function set($key, $value)
    {
        return $this->redis->set($key, $value);
    }

    public function del($key)
    {
        return $this->redis->del($key);
    }

    public function flush()
    {
        $this->redis->flushDb();
    }
}
