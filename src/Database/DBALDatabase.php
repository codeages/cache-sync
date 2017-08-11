<?php

namespace Codeages\CacheSync\Database;

use Codeages\CacheSync\Database;
use Doctrine\DBAL\Connection;

class DBALDatabase implements Database
{
    /**
     * @var Connection
     */
    protected $dbal;

    public function __construct(Connection $db)
    {
        $this->dbal = $db;
    }

    public function query($sql, $params = array())
    {
        $stmt = $this->dbal->executeQuery($sql, $params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function execute($sql, $params = array())
    {
        return $this->dbal->executeUpdate($sql, $params);
    }

    public function reconnect()
    {
        if ($this->dbal->ping() === false) {
            $this->dbal->close();
            $this->dbal->connect();
        }
    }
}
