<?php

namespace App\Controllers;

use Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function __construct() {
        requireGuest();
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = $this->validateRegistration();

            if (!empty($_SESSION['errors'])) {
                redirect('register');
            }

            $userModel = new User();

            if ($userModel->create($data)) {
                $user = $userModel->getUserByEmail($data['email']);
                $this->generateSessionForAuthenticatedUser($user);
                $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
                header('Location: ' . $baseUrl . '/');
            } else {
                $_SESSION['errors'][] = 'Registration failed. Please contact support.';
                redirect('register');
            }
        } else {
            $this->render('register');
        }
    }

    private function validateRegistration()
    {
        $data['name'] = trim($_POST['name']);
        $data['email'] = trim($_POST['email']);
        $data['password'] = trim($_POST['password']);

        if (empty($data['name'])) {
            $_SESSION['errors'][] = "Name required.";
        }

        if (empty($data['email'])) {
            $_SESSION['errors'][] = "Email required.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['errors'][] = 'Invalid email format.';
        }

        if (empty($data['password'])) {
            $_SESSION['errors'][] = "Password required.";
        } elseif (strlen($data['password']) < 6) {
            $_SESSION['errors'][] = "Password must be at least 6 characters long.";
        }

        if (!empty($data['email'])) {
            $user = (new User())->getUserByEmail($data['email']);

            if (!empty($user)) {
                $_SESSION['errors'][] = "Email already used";
            }
        }
        return $data;
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $this->validateLogin();

            if (empty($_SESSION['errors'])) {
                $user = (new User())->getUserByEmail($data['email']);
            
                if ($user && password_verify($data['password'], $user['password'])) {
                    $this->generateSessionForAuthenticatedUser($user);
                    redirect();
                } else {
                    $_SESSION['errors'][] = 'Invalid email or password.';
                    redirect('login');
                }
            } else {
                redirect('login');
            }
        } else {
            $this->render('login');
        }
    }

    public function validateLogin()
    {
        $data['email'] = trim($_POST['email']);
        $data['email'] = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
        $data['password'] = $_POST['password'];

        $_SESSION['errors'] = [];

        if (empty($data['email'])) {
            $_SESSION['errors'][] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['errors'][] = 'Invalid email format.';
        }

        if (empty($data['password'])) {
            $_SESSION['errors'][] = 'Password is required.';
        }

        return $data;
    }

    private function generateSessionForAuthenticatedUser($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_type'] = $user['type'];
    }
}
