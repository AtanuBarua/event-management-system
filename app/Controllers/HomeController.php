<?php

require_once 'app/Models/User.php';

class HomeController
{
    public function index() {
        $userModel = new User();
        $users = $userModel->getAllUsers();
        include 'views/index.php';
    }
}