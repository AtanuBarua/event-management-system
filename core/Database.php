<?php

namespace Core;

use PDO;
use PDOException;

class Database
{
    private $pdo;

    public function __construct() {
        $config = include('../config/database.php');
        $dsn = "mysql:host=" . $config['host'] . ";dbname=" . $config['dbname'];

        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function getPdo() {
        return $this->pdo;
    }
}
