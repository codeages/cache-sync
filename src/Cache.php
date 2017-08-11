<?php

namespace Codeages\CacheSync;

interface Cache
{
    public function get($key);

    public function set($key, $value);

    public function del($key);

    public function flush();

    /**
     * 如果连接已断开，就重连。
     */
    public function reconnect();
}
