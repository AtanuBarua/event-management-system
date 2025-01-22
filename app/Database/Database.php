<?php

class Database
{
    private $pdo;

    public function __construct()
    {
        $config = include('config/database.php');
        $dsn = "mysql:host=" . $config['host'] . ";dbname=" . $config['dbname'];
        $this->pdo = new PDO($dsn, $config['username'], $config['password']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}
