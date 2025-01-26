<?php

require_once 'app/Database/Database.php';

class EventAttendee
{
    private $pdo;

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getPdo();
    }

    public function create($eventId, $userId) {
        $this->pdo->beginTransaction();
        try {
            $sql = "SELECT capacity, (SELECT COUNT(*) FROM event_attendees WHERE event_id=:id) AS current_attendees FROM events WHERE id=:id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $eventId]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (empty($event)) {
                $this->pdo->rollBack();
                return [false, 'No event found'];
            }

            if ($event['current_attendees'] >= $event['capacity']) {
                $this->pdo->rollBack();
                return [false, 'Event is fully booked'];
            }

            $sql = "INSERT INTO event_attendees (event_id, user_id) VALUES (:event_id, :user_id)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'event_id' => $eventId,
                'user_id' => $userId
            ]);
            $this->pdo->commit();
            return [true, 'Event registration successful'];
        } catch (\Throwable $th) {
            $this->pdo->rollBack();
            return [false, 'Something went wrong'];
        }
    }
}