<?php

namespace App\Repositories;

use App\Models\Task;
use App\Core\Database\Database;
use PDO;

class TaskRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function save(Task $task): ?Task
    {
        $sql = $task->getId() === null
            ? "INSERT INTO tasks (user_id, title, description, due_date, priority, status, reminder_type, reminder_date, reminder_time, last_reminder_sent, created_at, updated_at) 
               VALUES (:user_id, :title, :description, :due_date, :priority, :status, :reminder_type, :reminder_date, :reminder_time, :last_reminder_sent, :created_at, :updated_at)"
            : "UPDATE tasks SET title = :title, description = :description, due_date = :due_date, 
               priority = :priority, status = :status, reminder_type = :reminder_type, reminder_date = :reminder_date, 
               reminder_time = :reminder_time, last_reminder_sent = :last_reminder_sent, updated_at = :updated_at 
               WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $params = [
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'due_date' => $task->getDueDate(),
            'priority' => $task->getPriority(),
            'status' => $task->getStatus(),
            'reminder_type' => $task->getReminderType(),
            'reminder_date' => $task->getReminderDate(),
            'reminder_time' => $task->getReminderTime(),
            'last_reminder_sent' => $task->getLastReminderSent(),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($task->getId() === null) {
            $params['user_id'] = $task->getUserId();
            $params['created_at'] = $task->getCreatedAt();
        } else {
            $params['id'] = $task->getId();
        }

        if ($stmt->execute($params)) {
            if ($task->getId() === null) {
                $task = new Task(
                    $task->getUserId(),
                    $task->getTitle(),
                    $task->getDescription(),
                    $task->getDueDate(),
                    $task->getPriority(),
                    $task->getStatus(),
                    $task->getReminderType(),
                    $task->getReminderDate(),
                    $task->getReminderTime(),
                    $task->getLastReminderSent(),
                    (int)$this->db->lastInsertId()
                );
            }
            return $task;
        }

        return null;
    }

    public function findById(int $id, int $userId): ?Task
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $data = $stmt->fetch();

        return $data ? $this->hydrate($data) : null;
    }

    public function findByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $userId]);
        $data = $stmt->fetchAll();

        return array_map([$this, 'hydrate'], $data);
    }

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :user_id");
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    /**
 * Находит задачи для отправки напоминаний
 */
public function findTasksForReminder(string $currentDateTime): array
{
    $currentDate = date('Y-m-d', strtotime($currentDateTime));
    $currentTime = date('H:i:00', strtotime($currentDateTime));
    
    error_log("🔔 Searching reminders for: Date={$currentDate}, Time={$currentTime}");
    
    //запрос с уникальными именами параметров
    $sql = "SELECT * FROM tasks 
            WHERE reminder_type != 'none' 
            AND status = 'pending'
            AND reminder_time = :current_time
            AND (
                -- Однократные напоминания: точное совпадение даты
                (reminder_type = 'one_time' AND reminder_date = :current_date_1)
                OR 
                -- Ежедневные напоминания: всегда срабатывает если время совпадает
                (reminder_type = 'daily' AND (last_reminder_sent IS NULL OR DATE(last_reminder_sent) != :current_date_2))
                OR
                -- Еженедельные напоминания: совпадение дня недели
                (reminder_type = 'weekly' AND DAYOFWEEK(reminder_date) = DAYOFWEEK(:current_date_3) AND (last_reminder_sent IS NULL OR DATE(last_reminder_sent) != :current_date_4))
                OR
                -- Ежемесячные напоминания: совпадение дня месяца  
                (reminder_type = 'monthly' AND DAY(reminder_date) = DAY(:current_date_5) AND (last_reminder_sent IS NULL OR DATE(last_reminder_sent) != :current_date_6))
            )";
    
    try {
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'current_time' => $currentTime,
            'current_date_1' => $currentDate,
            'current_date_2' => $currentDate,
            'current_date_3' => $currentDate,
            'current_date_4' => $currentDate,
            'current_date_5' => $currentDate,
            'current_date_6' => $currentDate
        ]);
        
        $data = $stmt->fetchAll();
        error_log("🔔 Found " . count($data) . " tasks for reminding");
        
        return array_map([$this, 'hydrate'], $data);
    } catch (\Exception $e) {
        error_log("❌ SQL Error in findTasksForReminder: " . $e->getMessage());
        error_log("❌ SQL: " . $sql);
        return [];
    }
}

    /**
     * Находит все задачи с напоминаниями (для отладки)
     */
    public function findAllTasksWithReminders(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE reminder_type != 'none'");
        $stmt->execute();
        $data = $stmt->fetchAll();
        
        return array_map([$this, 'hydrate'], $data);
    }

    private function hydrate(array $data): Task
    {
        $task = new Task(
            $data['user_id'],
            $data['title'],
            $data['description'],
            $data['due_date'],
            $data['priority'],
            $data['status'],
            $data['reminder_type'] ?? Task::REMINDER_TYPE_NONE,
            $data['reminder_date'] ?? null,
            $data['reminder_time'] ?? null,
            $data['last_reminder_sent'] ?? null,
            $data['id']
        );

        return $task;
    }

}
