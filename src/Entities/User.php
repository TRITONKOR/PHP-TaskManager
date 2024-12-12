<?php

declare(strict_types=1);

namespace Alex\TaskManagerApp\Entities;

use Alex\TaskManagerApp\DB\Database;
use PDO;

class User
{
    private static PDO $db;

    public function __construct(
        private int $id,
        private string $username,
        private string $email,
        private string $password,
    )
    {
        if (!isset(self::$db)) {
            self::getDb();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function save(): ?self
    {
        if ($this->id) {
            $stmt = self::getDb()->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
            $success = $stmt->execute(['username' => $this->username, 'email' => $this->email, 'id' => $this->id]);

        } else {
            $stmt = self::getDb()->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $success = $stmt->execute(['username' => $this->username, 'email' => $this->email, 'password' => $this->password]);
            if ($success) {
                $this->id = (int) self::getDb()->lastInsertId();
            }
        }

        return $success ? $this : null;
    }

    public static function delete(int $id): bool
    {
        $stmt = self::getDb()->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function getById(int $id): ?self
    {
        $stmt = self::getDb()->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $userData ? self::rowMap($userData) : null;
    }

    public static function getByUsername(string $username): ?self
    {
        $stmt = self::getDb()->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $userData ? self::rowMap($userData) : null;
    }

    public static function getAll(): array
    {
        $stmt = self::getDb()->prepare("SELECT * FROM users");
        $stmt->execute();
        $usersData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($userData) {
            return self::rowMap($userData);
        }, $usersData);
    }

    private static function getDb(): PDO
    {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
        return self::$db;
    }

    private static function rowMap(array $data): self
    {
        return new self(
            id: (int)$data['id'],
            username: $data['username'],
            email: $data['email'],
            password: $data['password']
        );
    }
}