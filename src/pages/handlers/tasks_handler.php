<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /login");
    exit();
}

use Alex\TaskManagerApp\Entities\TaskStatus;
use Alex\TaskManagerApp\Services\TaskManager;

$taskManager = new TaskManager();
$userId = $_SESSION['user_id'];

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!empty($title) && !empty($description)) {
        $status = TaskStatus::InProgress;
        $task = $taskManager->addTask($title, $description, $status, $userId);

        // Перенаправлення після успіху
        header("Location: /tasks");
        exit();
    } else {
        // Помилка, якщо поля порожні
        header("Location: /tasks");
        exit();
    }
} elseif ($requestMethod === 'PUT') {
    $inputData = json_decode(file_get_contents("php://input"), true);

    $taskId = $inputData['task_id'] ?? null;
    $title = trim($inputData['title'] ?? '');
    $description = trim($inputData['description'] ?? '');
    $status = TaskStatus::from($inputData['status']) ?? TaskStatus::InProgress;

    if ($taskId && !empty($title) && !empty($description)) {
            $task = $taskManager->getTaskById($taskId);
            $taskManager->updateTask($task, $title, $description, $status);
            echo json_encode(['status' => 'success', 'redirect' => '/tasks']);
            exit();
    } else {
        echo "Будь ласка, заповніть всі поля для оновлення.";
    }
} elseif ($requestMethod === 'DELETE') {
    $inputData = json_decode(file_get_contents("php://input"), true);

    $taskId = $inputData['task_id'] ?? null;

    if ($taskId) {
        $taskManager->deleteTask($taskId);
        echo json_encode(['status' => 'success', 'redirect' => '/tasks']);
        exit();
    } else {
        echo "Задача для видалення не вказана.";
    }
} else {
    echo "Непідтримуваний метод запиту.";
}