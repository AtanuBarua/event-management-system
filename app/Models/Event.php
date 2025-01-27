<?php

namespace App\Models;

use Core\Model;
use PDO;

class Event extends Model
{
    const PAGE_LIMIT = 5;
    const SORT_ASCENDING = 'asc';
    const SORT_DESCENDING = 'desc';

    public function getAllEvents($limit = self::PAGE_LIMIT, $offset = 0, $sortOrder = self::SORT_DESCENDING, $search = []) {
        $limit = (int) $limit;
        $offset = (int) $offset;
        $searchName = $search['name'] ?? '';
        $sql = "SELECT * FROM events WHERE name LIKE :name ORDER BY id $sortOrder LIMIT $limit OFFSET $offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', '%' . $searchName . '%', PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO events (name, description, capacity, created_by) VALUES (:name, :description, :capacity, :created_by)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'description' => $data['description'],
            'capacity' => $data['capacity'],
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

    public function getTotalEventsCount($search) {
        $sql = "SELECT COUNT(*) as total FROM events WHERE name LIKE :name";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':name', '%' . $search['name'] . '%', PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
