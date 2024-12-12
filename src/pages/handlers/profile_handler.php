<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

use Alex\TaskManagerApp\Entities\User;
use Alex\TaskManagerApp\Services\UserService;

$userId = $_SESSION['user_id'];
$user = User::getById($userId);

$userService = new UserService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (password_verify($currentPassword, $user->getPassword())) {
        if ($newPassword === $confirmPassword) {
            $user->setPassword(password_hash($newPassword, PASSWORD_DEFAULT));
            $userService->updateUser($user, ['password' => $user->getPassword()]);
            $successMessage = "Password successfully changed!";
        } else {
            $errorMessage = "New passwords do not match!";
        }
    } else {
        $errorMessage = "Current password is incorrect!";
    }
}