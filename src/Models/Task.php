<?php

namespace App\Models;

class Task
{
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';

    // ⬇️ НОВОЕ: Типы напоминаний
    public const REMINDER_TYPE_NONE = 'none';
    public const REMINDER_TYPE_ONE_TIME = 'one_time';
    public const REMINDER_TYPE_DAILY = 'daily';
    public const REMINDER_TYPE_WEEKLY = 'weekly';
    public const REMINDER_TYPE_MONTHLY = 'monthly';

    private ?int $id;
    private int $userId;
    private string $title;
    private ?string $description;
    private ?string $dueDate;
    private string $priority;
    private string $status;
    
    // ⬇️ НОВЫЕ ПОЛЯ для напоминаний
    private string $reminderType;
    private ?string $reminderDate;
    private ?string $reminderTime;
    private ?string $lastReminderSent;
    private string $createdAt;
    private string $updatedAt;

    public function __construct(
        int $userId,
        string $title,
        ?string $description = null,
        ?string $dueDate = null,
        string $priority = self::PRIORITY_MEDIUM,
        string $status = self::STATUS_PENDING,
        string $reminderType = self::REMINDER_TYPE_NONE,
        ?string $reminderDate = null,
        ?string $reminderTime = null,
        ?string $lastReminderSent = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->description = $description;
        $this->dueDate = $dueDate;
        $this->priority = $priority;
        $this->status = $status;
        $this->reminderType = $reminderType;
        $this->reminderDate = $reminderDate;
        $this->reminderTime = $reminderTime;
        $this->lastReminderSent = $lastReminderSent;
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
    }

    // Существующие геттеры...
    public function getId(): ?int { return $this->id; }
    public function getUserId(): int { return $this->userId; }
    public function getTitle(): string { return $this->title; }
    public function getDescription(): ?string { return $this->description; }
    public function getDueDate(): ?string { return $this->dueDate; }
    public function getPriority(): string { return $this->priority; }
    public function getStatus(): string { return $this->status; }
    public function getCreatedAt(): string { return $this->createdAt; }
    public function getUpdatedAt(): string { return $this->updatedAt; }

    // ⬇️ НОВЫЕ геттеры для напоминаний
    public function getReminderType(): string { return $this->reminderType; }
    public function getReminderDate(): ?string { return $this->reminderDate; }
    public function getReminderTime(): ?string { return $this->reminderTime; }
    public function getLastReminderSent(): ?string { return $this->lastReminderSent; }

    // Существующие сеттеры...
    public function setTitle(string $title): void { $this->title = $title; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function setDueDate(?string $dueDate): void { $this->dueDate = $dueDate; }
    public function setPriority(string $priority): void { $this->priority = $priority; }
    public function setStatus(string $status): void { $this->status = $status; }

    // ⬇️ НОВЫЕ сеттеры для напоминаний
    public function setReminderType(string $reminderType): void { $this->reminderType = $reminderType; }
    public function setReminderDate(?string $reminderDate): void { $this->reminderDate = $reminderDate; }
    public function setReminderTime(?string $reminderTime): void { $this->reminderTime = $reminderTime; }
    public function setLastReminderSent(?string $lastReminderSent): void { $this->lastReminderSent = $lastReminderSent; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->dueDate,
            'priority' => $this->priority,
            'status' => $this->status,
            'reminder_type' => $this->reminderType,
            'reminder_date' => $this->reminderDate,
            'reminder_time' => $this->reminderTime,
            'last_reminder_sent' => $this->lastReminderSent,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }

    // ⬇️ НОВЫЕ методы для работы с напоминаниями
    public function hasReminder(): bool
    {
        return $this->reminderType !== self::REMINDER_TYPE_NONE;
    }

    public function isRecurringReminder(): bool
    {
        return in_array($this->reminderType, [
            self::REMINDER_TYPE_DAILY,
            self::REMINDER_TYPE_WEEKLY,
            self::REMINDER_TYPE_MONTHLY
        ]);
    }

    public function getNextReminderDateTime(): ?string
    {
        if (!$this->hasReminder() || !$this->reminderDate || !$this->reminderTime) {
            return null;
        }

        return $this->reminderDate . ' ' . $this->reminderTime . ':00';
    }
}