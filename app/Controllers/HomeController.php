<?php

require_once 'app/Models/Event.php';

class HomeController
{
    public function index()
    {
        $eventModel = new Event();

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            try {
                list($errors, $data) = $this->validationEvent();
                if (empty($errors)) {
                    if (!empty($_POST['id'])) {
                        if ($eventModel->update($_POST['id'], $data)) {
                            $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
                            header('Location: ' . $baseUrl . '/');
                        } else {
                            $errors[] = 'Something went wrong. Please contact support.';
                            include 'views/index.php';
                        }
                    } else {
                        if ($eventModel->create($data)) {
                            $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
                            header('Location: ' . $baseUrl . '/');
                        } else {
                            $errors[] = 'Something went wrong. Please contact support.';
                            include 'views/index.php';
                        }
                    }
                } else {
                    include 'views/index.php';
                }
            } catch (\Throwable $th) {
                error_log($th->getMessage());
                $errors[] = 'Something went wrong';
                include 'views/index.php';
            }
        } else {
            $limit = Event::PAGE_LIMIT;
            $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
            $offset = ($page - 1) * $limit;
            $sortOrder = isset($_GET['sortOrder']) && $_GET['sortOrder'] === Event::SORT_ASCENDING ? Event::SORT_ASCENDING : Event::SORT_DESCENDING;
            $events = $eventModel->getAllEvents($limit, $offset, $sortOrder);
            $totalEvents = $eventModel->getTotalEventsCount();
            $totalPages = ceil($totalEvents/$limit);
            include 'views/index.php';
        }
    }

    public function validationEvent()
    {
        $errors = [];
        $data['name'] = trim($_POST['name']);
        $data['description'] = trim($_POST['description']);

        if (empty($data['name'])) {
            $errors[] = 'Event name is required';
        }

        if (empty($data['description'])) {
            $errors[] = 'Event description is required';
        }

        return [$errors, $data];
    }

    public function deleteEvent() {
        $eventModel = new Event();
        $events = $eventModel->getAllEvents();
        $errors = [];
        try {
            $id = $_POST['id'] ?? null;

            if (!$id) {
                $errors[] = 'ID is required';
            }

            $event = $eventModel->getEventById($id);

            if (empty($event)) {
                $errors[] = 'No event found';
            } 

            if (!empty($errors)) {
                $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
                header('Location: ' . $baseUrl . '/');
            }
            
            if ($eventModel->delete($id)) {
                $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
                header('Location: ' . $baseUrl . '/');
            } else {
                $errors[] = 'Something went wrong. Please contact support.';
                include 'views/index.php';
            }
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            $errors[] = 'Something went wrong';
            include 'views/index.php';
        }
    }
}
