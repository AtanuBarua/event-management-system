<?php

namespace App\Models;
use Core\Model;
use PDO;
class User extends Model
{
    const TYPE_ADMIN = 1;
    const TYPE_USER = 2;

    public function isAdmin($type) {
        return $type == self::TYPE_ADMIN;
    }

    public function getAllUsers()
    {
        $stmt = $this->pdo->query("SELECT * FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        try {
            $sql = "INSERT INTO users (name, email, password) VALUES (:name, :email, :password)";
            $stmt = $this->pdo->prepare($sql);

            $stmt->execute([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            ]);

            return $stmt->rowCount() > 0;
        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return false;
        }
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
