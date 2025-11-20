<?php

namespace App\Models;

class UserState
{
    public const STATE_NONE = 'none';
    public const STATE_AWAITING_TASK_TITLE = 'awaiting_task_title';
    public const STATE_AWAITING_TASK_DUE_DATE = 'awaiting_task_due_date';
    public const STATE_AWAITING_TASK_PRIORITY = 'awaiting_task_priority';

    public const STATE_AWAITING_REMINDER_TYPE = 'awaiting_reminder_type';
    public const STATE_AWAITING_REMINDER_DATE = 'awaiting_reminder_date';
    public const STATE_AWAITING_REMINDER_TIME = 'awaiting_reminder_time';

    private int $userId;
    private string $state;
    private array $tempData;

    public function __construct(int $userId, string $state = self::STATE_NONE, array $tempData = [])
    {
        $this->userId = $userId;
        $this->state = $state;
        $this->tempData = $tempData;
    }

    public function getUserId(): int { return $this->userId; }
    public function getState(): string { return $this->state; }
    public function getTempData(): array { return $this->tempData; }
    public function getTempDataValue(string $key, $default = null) { return $this->tempData[$key] ?? $default; }

    public function setState(string $state): void { $this->state = $state; }
    public function setTempData(array $tempData): void { $this->tempData = $tempData; }
    public function setTempDataValue(string $key, $value): void { $this->tempData[$key] = $value; }
    public function clearTempData(): void { $this->tempData = []; }

}
