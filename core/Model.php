<?php

namespace Core;

class Model {
    
    protected $pdo;

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getPdo();
    }
}
