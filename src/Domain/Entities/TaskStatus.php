<?php

declare(strict_types=1);

namespace Camal\AppDataMapperLocal\Domain\Entities;

class TaskStatus
{
    private string $title;
    const STATUS_TITLES = [
        "created" => "новая",
        "in_progress" => "в работе",
        "completed" => "завершена",
        "canceled" => "отменена"
    ];

    public function __construct(
        private string $code
    ) {
        $this->changeStatus($this->code);
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function tryChangeToInProgress(): bool
    {
        if ($this->code === "created") {
            $this->changeStatus("in_progress");
            return true;
        }
        return false;
    }

    public function tryChangeToCompleted(): bool
    {
        if ($this->code === "in_progress") {
            $this->changeStatus("completed");
            return true;
        }
        return false;
    }

    public function tryChangeToCanceled(): bool
    {
        if ($this->code === "created" || $this->code === "in_progress") {
            $this->changeStatus("canceled");
            return true;
        }
        return false;
    }

    private function changeStatus($code): void
    {
        $this->code = $code;
        $this->title = self::STATUS_TITLES[$this->code];
    }
}
