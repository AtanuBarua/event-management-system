<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\Event;
use App\Models\EventAttendee;

class HomeController extends Controller
{
    public function __construct() {
        requireAuth();
    }
    
    public function index()
    {
        try {
            $eventModel = new Event();
            $limit = Event::PAGE_LIMIT;
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;
            $sortOrder = isset($_GET['sortOrder']) && $_GET['sortOrder'] === Event::SORT_ASCENDING ? Event::SORT_ASCENDING : Event::SORT_DESCENDING;
            $search['name'] = isset($_GET['name']) ? trim($_GET['name']) : '';
            $events = $eventModel->getAllEvents($limit, $offset, $sortOrder, $search);
            $userRegisteredEvents = (new EventAttendee())->getUserRegisteredEventIds($_SESSION['user_id']);
            $totalEvents = $eventModel->getTotalEventsCount($search);
            $totalPages = ceil($totalEvents / $limit);

            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                $data = $this->validateEvent();

                if (empty($_SESSION['errors'])) {
                    $this->handleEventSubmission($eventModel, $data);
                } 
                redirect();
            } else {
                $this->render('index', [
                    'events' => $events,
                    'page' => $page,
                    'totalPages' => $totalPages,
                    'sortOrder' => $sortOrder,
                    'userRegisteredEvents' => $userRegisteredEvents
                ]);
            }
        } catch (\Throwable $th) {
            $_SESSION['errors'][] = 'Something went wrong';
            redirect();
        }
    }

    private function validateEvent()
    {
        $data['name'] = trim($_POST['name']);
        $data['description'] = trim($_POST['description']);
        $data['capacity'] = trim($_POST['capacity']);

        if (empty($data['name'])) {
            $_SESSION['errors'][] = 'Event name is required';
        }

        if (empty($data['description'])) {
            $_SESSION['errors'][] = 'Event description is required';
        }

        if (empty($data['capacity'])) {
            $_SESSION['errors'][] = 'Capacity is required';
        }

        return $data;
    }

    private function handleEventSubmission($eventModel, $data)
    {
        $isUpdate = !empty($_POST['id']);
        $isSuccess = $isUpdate ? $eventModel->update($_POST['id'], $data) : $eventModel->create($data);
        
        if ($isSuccess) {
            $_SESSION['message'] = $isUpdate ? 'Event updated successfully' : 'Event created successfully';
        } else {
            $_SESSION['errors'][] = 'Something went wrong. Please contact support.';
        }
    }

    public function deleteEvent()
    {
        try {
            $eventModel = new Event();
            $id = $_POST['id'] ?? null;

            if (!$id) {
                $_SESSION['errors'][] = 'ID is required';
            }

            $event = $eventModel->getEventById($id);

            if (empty($event)) {
                $_SESSION['errors'][] = 'No event found';
            }

            if (!empty($_SESSION['errors'])) {
                $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
                header('Location: ' . $baseUrl . '/');
            }

            if (!$eventModel->delete($id)) {
                $_SESSION['errors'][] = 'Something went wrong. Please contact support.';
            } else {
                $_SESSION['message'] = 'Deleted successfully';
            }
            redirect();
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            $_SESSION['errors'][] = 'Something went wrong';
            redirect();
        }
    }

    public function eventRegistration() {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $eventAttendeeModel = new EventAttendee();

            if (empty($_POST['event_id'])) {
                $_SESSION['errors'][] = 'Please select event';
            }

            if (empty($_SESSION['errors'])) {
                $isAlreadyRegistered = $eventAttendeeModel->checkIfUserAlreadyRegistered($_SESSION['user_id'], $_POST['event_id']);
                
                if ($isAlreadyRegistered) {
                    $_SESSION['errors'][] = 'You have already registered for this event';
                    redirect();
                }

                $eventAttendeeModel->create($_POST['event_id'], $_SESSION['user_id']);
            } 
            redirect();
        }
    }

    public function eventAttendeesExport() {    
        try {
            $eventId = $_GET['event_id'] ?? null;
    
            if (!$eventId || !is_numeric($eventId)) {
                $_SESSION['errors'][] = 'Invalid event ID';
                redirect(); 
            }
    
            $users = (new EventAttendee())->getEventAttendeeListByEventId($eventId);
    
            if (empty($users)) {
                $_SESSION['errors'][] = 'No users found for the selected event';
                redirect();
            }
    
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="event_' . $eventId . '_users.csv"');
    
            $output = fopen('php://output', 'w');
            if ($output === false) {
                $_SESSION['errors'][] = 'Failed to create CSV output';
            }
    
            fputcsv($output, ['User ID', 'Name', 'Email', 'Event ID']);
    
            foreach ($users as $user) {
                fputcsv($output, $user);
            }
    
            fclose($output);
            exit();
    
        } catch (\Exception $e) {
            $_SESSION['errors'][] = 'Something went wrong';
            redirect(); 
        }
    }

    public function logout() {
        session_start();
        $_SESSION = [];
        session_destroy();
        redirect('login');
    }
}
