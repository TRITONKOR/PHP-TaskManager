<?php

declare(strict_types=1);

namespace Alex\TaskManagerApp\Services;

use Alex\TaskManagerApp\Entities\User;

class UserService
{
    public function getAllUsers(): array
    {
        return User::getAll();
    }

    public function createUser(string $username, string $email, string $password): ?User
    {
        return (new User(
            id: 0,
            username: $username,
            email: $email,
            password: password_hash($password, PASSWORD_DEFAULT)
        ))->save();
    }

    public function login(string $username, string $password): ?User
    {
        $user = User::getByUsername($username);

        if ($user && password_verify($password, $user->getPassword())) {
            error_log("Password verification successful for user: " . $username);
            return $user;
        } else {
            error_log("Password verification failed for user: " . $username);
        }

        return null;
    }

    public function updateUser(User $user, array $data): User
    {
        if (isset($data['username'])) {
            $user->setUsername($data['username']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $user->setPassword($data['password']);
        }

        return $user->save();
    }

    public function deleteUser(int $id): bool
    {
        return User::delete($id);
    }

    public function getUserById(int $id): ?User
    {
        return User::getById($id);
    }

    public function getUserByUsername(string $username): ?User
    {
        return User::getByUsername($username);
    }
}