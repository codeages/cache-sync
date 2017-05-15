<?php
namespace Codeages\CacheSync;

class CacheSync
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
        $row = array(
            'op' => $op,
            'k' => $key,
            'v' => empty($value) ? '' : $value,
            'created_time' => time(),
        );

        $this->db->insert($row);
    }
}
