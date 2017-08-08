<?php

namespace Codeages\CacheSync\Database;

use Codeages\CacheSync\Database;
use Codeages\CacheSync\Exception;

class PDODatabase implements Database
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function execute($sql, $params = array())
    {
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':'.$key, $value);
        }
        $success = $stmt->execute();

        if (!$success) {
            $error = $stmt->errorInfo();
            throw new Exception("[{$error[0]}] {$error[2]}");
        }

        return $stmt->rowCount();
    }

    public function query($sql, $params = array())
    {
        $stmt = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':'.$key, $value);
        }
        $success = $stmt->execute();

        if (!$success) {
            $error = $stmt->errorInfo();
            throw new Exception("[{$error[0]}] {$error[2]}");
        }

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
