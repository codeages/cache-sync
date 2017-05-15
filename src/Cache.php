<?php
namespace Codeages\CacheSync;

interface Cache
{
    public function get($key);

    public function set($key, $value);

    public function del($key);

    public function flush();
}