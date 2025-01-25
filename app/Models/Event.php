<?php

require_once 'app/Database/Database.php';

class Event
{
    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getPdo();
    }

    public function getAllEvents() {
        $stmt = $this->pdo->query("SELECT * FROM events");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        session_start();
        $sql = "INSERT INTO events (name, description, created_by) VALUES (:name, :description, :created_by)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'created_by' => $_SESSION['user_id'],
        ]);
        return $stmt->rowCount() > 0;
    }

    public function update($id, $data) {
        $sql = "UPDATE events SET name=:name, description=:description WHERE id=:id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'description' => $data['description']
        ]);
    }
}
