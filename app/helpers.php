<?php
session_start();

function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

function requireAuth() {
    if (!isAuthenticated()) {
        $_SESSION['errors'][] = "You must be logged in to access this page.";
        redirect('login');
    }
}

function redirect($url = '') {
    $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
    header('Location: ' . $baseUrl . '/' . $url);
    exit();
}

function requireGuest() {
    if (isAuthenticated()) {
        redirect();
    }
}