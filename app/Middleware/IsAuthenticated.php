<?php 

namespace App\Middleware;

class IsAuthenticated 
{
    // public function __construct() {
    //     session_start();
    //     if (empty($_SESSION['user_id'])) {
    //         $_SESSION['errors'][] = 'You must be logged in to access this page';
    //         $this->redirect('login');
    //     }
    // }

    // private function redirect($url = '')
    // {
    //     $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
    //     header('Location: ' . $baseUrl . '/' . $url);
    //     exit();
    // }
}