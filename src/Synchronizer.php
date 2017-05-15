<?php
namespace Codeages\CacheSync;

class Synchronizer
{
    protected $db;

    protected $table;

    protected $options;

    public function __construct(Database $db, Cache $cache,  $table, array $options)
    {
        $this->db = $db;
        $this->cache = $cache;
        $this->table = $table;
        $this->options = $options;
    }

    public function push($op, $key, $value = null)
    {
        $params = array(
            'op' => $op,
            'k' => $key,
            'v' => empty($value) ? '' : $value,
            'created_time' => time(),
        );

        $sql = "INSERT INTO {$this->table} (`op`, `k`, `v`, `created_time`) VALUES (:op, :k, :v, :created_time);";

        return $this->db->execute($sql, $params);
    }

    public function sync($limit = 100)
    {
        $limit = intval($limit);
        $cursor = $this->getCursor();
        $sql = "SELECT * FROM {$this->table} WHERE id > {$cursor} ORDER BY id ASC LIMIT {$limit}";

        $synced = 0;
        $rows = $this->db->query($sql);
        foreach ($rows as $row) {
            if ($row['op'] === 'del') {
                $this->cache->del($row['k']);
                $synced++;
            } elseif ($row['op'] === 'set') {
                $this->cache->set($row['k'], $row['v']);
                $synced++;
            }
        }

        $last = end($rows);
        $this->saveCursor($last['id']);

        return $synced;
    }

    protected function getCursor()
    {
        $path = $this->options['cursor_file'];
        if (!file_exists($path)) {
            return 0;
        }
        return intval(file_get_contents($path));
    }

    protected function saveCursor($cursor)
    {
        file_put_contents($this->options['cursor_file'], $cursor);
    }
}
