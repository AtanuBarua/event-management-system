<?php

use App\Controllers\AuthController;
use App\Controllers\HomeController;

return [
    '/' => [HomeController::class, 'index'],
    '/register' => [AuthController::class, 'register'],
    '/login' => [AuthController::class, 'login'],
    '/logout' => [HomeController::class, 'logout'],
    '/event/delete' => [HomeController::class, 'deleteEvent'],
    '/event/register' => [HomeController::class, 'eventRegistration'],
    '/event-attendees/export' => [HomeController::class, 'eventAttendeesExport'],
];
