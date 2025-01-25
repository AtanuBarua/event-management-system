<?php

require_once 'app/Database/Database.php';

class Event
{
    const PAGE_LIMIT = 5;
    const SORT_ASCENDING = 'asc';
    const SORT_DESCENDING = 'desc';

    private $pdo;

    public function __construct()
    {
        $database = new Database();
        $this->pdo = $database->getPdo();
    }

    public function getAllEvents($limit = self::PAGE_LIMIT, $offset = 0, $sortOrder = self::SORT_DESCENDING) {
        $limit = (int) $limit;
        $offset = (int) $offset;
        $sql = "SELECT * FROM events ORDER BY id $sortOrder LIMIT $limit OFFSET $offset";
        $stmt = $this->pdo->query($sql);
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

    public function delete($id) {
        $sql = "DELETE from events WHERE id=:id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function getEventById($id) {
        $sql = "SELECT * FROM events WHERE id=:id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTotalEventsCount() {
        $sql = "SELECT COUNT(*) as total FROM events";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
