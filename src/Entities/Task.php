<?php

declare(strict_types=1);

namespace Alex\TaskManagerApp\Entities;

use DateTime;
use PDO;
use Alex\TaskManagerApp\DB\Database;
use Alex\TaskManagerApp\Entities\TaskStatus;

class Task
{
    private static PDO $db;

    public function __construct(
        private int $id,
        private string $title,
        private string $description,
        private TaskStatus $status,
        private User $user,
        private DateTime $createdAt,
        private DateTime $updatedAt
    )
    {
        if (!isset(self::$db)) {
            self::getDb();
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getStatus(): TaskStatus
    {
        return $this->status;
    }

    public function setStatus(TaskStatus $status): void
    {
        $this->status = $status;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function save(): ?self
    {
        if ($this->id) {
            $stmt = self::getDb()->prepare(
                "UPDATE tasks SET title = :title, description = :description, status = :status, creator_id = :creator_id, \"updatedAt\" = :updatedAt WHERE id = :id"
            );
            $success = $stmt->execute([
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status->value,
                'creator_id' => $this->user->getId(),
                'updatedAt' => $this->updatedAt->format('Y-m-d H:i'),
                'id' => $this->id,
            ]);

        } else {
            $stmt = self::getDb()->prepare(
                "INSERT INTO tasks (title, description, status, creator_id, \"createdAt\", \"updatedAt\")
                 VALUES (:title, :description, :status, :creator_id, :createdAt, :updatedAt)"
            );
            $success = $stmt->execute([
                'title' => $this->title,
                'description' => $this->description,
                'status' => $this->status->value,
                'creator_id' => $this->user->getId(),
                'createdAt' => $this->createdAt->format('Y-m-d H:i'),
                'updatedAt' => $this->updatedAt->format('Y-m-d H:i')
            ]);
            if ($success) {
                $this->id = (int)self::getDb()->lastInsertId();
            }
        }
        return $success ? $this : null;
    }

    public static function delete(int $id): bool
    {
        $stmt = self::getDb()->prepare("DELETE FROM tasks WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public static function getById(int $id): ?self
    {
        $stmt = self::getDb()->prepare("SELECT * FROM tasks WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $taskData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $taskData ? self::rowMap($taskData) : null;
    }

    public static function getByTitle(string $title): ?self
    {
        $stmt = self::getDb()->prepare("SELECT * FROM tasks WHERE title = :title");
        $stmt->execute(['title' => $title]);
        $taskData = $stmt->fetch(PDO::FETCH_ASSOC);

        return $taskData ? self::rowMap($taskData) : null;
    }

    public static function getAll(): array
    {
        $stmt = self::getDb()->prepare("SELECT * FROM tasks");
        $stmt->execute();
        $tasksData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($tasksData) {
            return self::rowMap($tasksData);
        }, $tasksData);
    }

    public static function getAllByUserId(int $userId): array {

        $stmt = self::getDb()->prepare("SELECT * FROM tasks WHERE creator_id = :userId");
        $stmt->execute(['userId' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($row) => self::rowMap($row), $rows);

    }

    private static function rowMap(array $data): self
    {
        $createdAt = self::parseDate($data['createdAt'] ?? null);
        $updatedAt = self::parseDate($data['updatedAt'] ?? null);

        return new self(
            id: (int)$data['id'],
            title: $data['title'],
            description: $data['description'],
            status: TaskStatus::from($data['status']),
            user: User::getById((int)$data['creator_id']),
            createdAt: $createdAt,
            updatedAt: $updatedAt
        );
    }

    private static function parseDate(?string $dateString): DateTime
    {
        try {
            return new DateTime($dateString);
        } catch (DateMalformedStringException $e) {
            error_log("Invalid date format: " . $e->getMessage());
            return new DateTime();
        }
    }

    private static function getDb(): PDO
    {
        if (!isset(self::$db)) {
            self::$db = Database::getInstance();
        }
        return self::$db;
    }
}
