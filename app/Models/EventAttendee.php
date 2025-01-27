<?php

namespace App\Models;

use Core\Model;
use PDO;

class EventAttendee extends Model
{
    public function create($eventId, $userId) {
        try {
            if (!$this->pdo->inTransaction()) {
                $this->pdo->beginTransaction();
            }
            $sql = "SELECT capacity, (SELECT COUNT(*) FROM event_attendees WHERE event_id=:id) AS current_attendees FROM events WHERE id=:id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['id' => $eventId]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (empty($event)) {
                $_SESSION['errors'][] = 'No event found';
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                return;
            }

            if ($event['current_attendees'] >= $event['capacity']) {
                $_SESSION['errors'][] = 'Event is fully booked';
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                return;
            }

            $sql = "INSERT INTO event_attendees (event_id, user_id) VALUES (:event_id, :user_id)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'event_id' => $eventId,
                'user_id' => $userId
            ]);
            $this->pdo->commit();
            $_SESSION['message'] = 'Event registration successful';
        } catch (\Throwable $th) {
            print_r($th->getMessage());

            $_SESSION['errors'][] = 'Something went wrong';
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return;
        }
    }
}