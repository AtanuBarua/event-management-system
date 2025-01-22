<?php

require_once 'app/Models/User.php';

class AuthController
{
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            list($errors, $data) = $this->validate();

            if (!empty($errors)) {
                include 'views/register.php';
                exit;
            }

            if ((new User())->create($data)) {
                $baseUrl = dirname($_SERVER['SCRIPT_NAME']);
                header('Location: ' . $baseUrl . '/');
            } else {
                $errors[] = 'Registration failed. Please contact support.';
                include 'views/register.php';
            }
        } else {
            include 'views/register.php';
        }
    }

    private function validate()
    {
        $data['name'] = trim($_POST['name']);
        $data['email'] = trim($_POST['email']);
        $data['password'] = trim($_POST['password']);
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = "Name required.";
        }

        if (empty($data['email'])) {
            $errors[] = "Email required.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        }

        if (empty($data['password'])) {
            $errors[] = "Password required.";
        } elseif (strlen($data['password']) < 6) {
            $errors[] = "Password must be at least 6 characters long.";
        }

        if (!empty($data['email'])) {
            $user = (new User())->getUserByEmail($data['email']);

            if (!empty($user)) {
                $errors[] = "Email already used";
            }
        }

        return [$errors, $data];
    }
}
