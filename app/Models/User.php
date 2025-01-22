<?php

require_once 'app/Database/Database.php';

class User 
{
    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getPdo();
    }

    public function getAllUsers()
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}