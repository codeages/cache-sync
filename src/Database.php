<?php

namespace Codeages\CacheSync;

interface Database
{
    public function query($sql, $params = array());

    public function execute($sql, $params = array());

    public function reconnect();
}
