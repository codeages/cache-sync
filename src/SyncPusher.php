<?php

namespace Codeages\CacheSync;

class SyncPusher
{
    protected $db;

    protected $table;

    public function __construct(Database $db, $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    public function push($op, $key, $value = null)
    {
        $params = array(
            'op' => $op,
            'k' => $key,
            'v' => empty($value) ? '' : $value,
            't' => time(),
        );

        $sql = "INSERT INTO {$this->table} (`op`, `k`, `v`, `t`) VALUES (:op, :k, :v, :t);";

        return $this->db->execute($sql, $params);
    }
}
