<?php
namespace Codeages\CacheSync;

interface Cache
{
    public function set($key, $value);

    public function del($key);
}