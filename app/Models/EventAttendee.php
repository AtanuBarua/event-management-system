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

    public function checkIfUserAlreadyRegistered($userId, $eventId) {
        $sql = "SELECT COUNT(*) FROM event_attendees WHERE user_id = :user_id AND event_id = :event_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'event_id' => $eventId
        ]);

        if ($stmt->fetchColumn() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getEventAttendeeListByEventId($eventId) {
        $sql = "SELECT users.id as user_id, users.name, users.email, event_attendees.event_id 
                FROM event_attendees 
                INNER JOIN USERS ON event_attendees.user_id = users.id
                WHERE event_attendees.event_id = :event_id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['event_id' => $eventId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserRegisteredEventIds($userId) {
        $sql = "SELECT event_id from event_attendees WHERE user_id=:user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
}