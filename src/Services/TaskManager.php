<?php

declare(strict_types=1);

namespace Alex\TaskManagerApp\Services;

use DateTime;
use Alex\TaskManagerApp\Entities\Task;
use Alex\TaskManagerApp\Entities\TaskStatus;
use Alex\TaskManagerApp\Entities\User;

class TaskManager
{
    public function addTask(string $title, string $description, TaskStatus $status, int $userId): Task
    {
        $user = User::getById($userId);

        return (new Task(
            id: 0,
            title: $title,
            description: $description,
            status: $status,
            user: $user,
            createdAt: new DateTime(),
            updatedAt: new DateTime()
        ))->save();
    }

    public function updateTask(Task $task, string $title, string $description, TaskStatus $status): Task
    {
        $task->setTitle($title);
        $task->setDescription($description);
        $task->setStatus($status);
        $task->setUpdatedAt(new DateTime());

        return $task->save();
    }

    public function deleteTask(int $id): bool
    {
        return Task::delete($id);
    }

    public function getTaskByTitle(string $title): ?Task
    {
        return Task::getByTitle($title);
    }

    public function getTaskById(int $id): ?Task
    {
        return Task::getById($id);
    }

    public function getAllTasks(): array
    {
        return Task::getAll();
    }

    public function getAllTasksByUser(int $userId): array
    {
        return Task::getAllByUserId($userId);
    }
}