<?php

namespace Codeages\CacheSync\Cache;

use Codeages\CacheSync\Cache;
use Redis;

class RedisCache implements Cache
{
    protected $host;

    protected $port;

    protected $options;

    /**
     * @var Redis
     */
    protected $redis;

    public function __construct($host, $port, $options = array())
    {
        $this->host = $host;
        $this->port = $port;
        $this->options = array_merge([
            'serializer' => Redis::SERIALIZER_PHP,
            'persistent' => true,
            'timeout' => 10, // 超时时间10s
            'retry_interval' => 1000, // 连接重试间隔时间1s
        ], $options);

        $this->redis = new Redis();
        $this->connect();
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

    public function reconnect()
    {
        try {
            $this->redis->ping();
        } catch(\RedisException $e) {
            $this->connect();
        }
    }

    protected function connect()
    {
        if ($this->options['persistent']) {
            $this->redis->pconnect($this->host, $this->port);
        } else {
            $this->redis->connect($this->host, $this->port);
        }
        $this->redis->setOption(Redis::OPT_SERIALIZER, $this->options['serializer']);
    }
}
