<?php

namespace Codeages\CacheSync;

use Redis;

class SyncPuller
{
    protected $db;

    protected $table;

    protected $options;

    public function __construct(Database $db, Cache $cache, array $options = [])
    {
        $this->db = $db;
        $this->cache = $cache;
        $this->options = array_merge([
            'table' => 'cache_sync',
            'redis_serializer' => Redis::SERIALIZER_PHP,
        ], $options);

        if (empty($options['cursor_file'])) {
            throw new \InvalidArgumentException('cursor_file option must be give.');
        }
    }

    public function pull($limit = 100)
    {
        $limit = intval($limit);
        $cursor = $this->getCursor();
        $sql = "SELECT * FROM {$this->options['table']} WHERE id > {$cursor} ORDER BY id ASC LIMIT {$limit}";

        $synced = 0;
        $rows = $this->db->query($sql);
        foreach ($rows as $row) {
            if ($row['op'] === 'del') {
                $this->cache->del($row['k']);
                ++$synced;
            } elseif ($row['op'] === 'set') {
                $this->cache->set($row['k'], $row['v']);
                ++$synced;
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
