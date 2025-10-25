<?php

declare(strict_types=1);

namespace Camal\AppDataMapperLocal\Domain\Entities;

class TaskPriority
{
    private string $title;

    const PRIORITY_TITLES = [
        "low" => "низкий",
        "ordinary" => "средний",
        "high" => "высокий",
        "extra" => "очень высокий"
    ];

    public function __construct(
        private string $code
    ) {
        $this->title = self::PRIORITY_TITLES[$this->code];
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getTitle()
    {
        return $this->title;
    }
    
    public function changeToLowPriority(): void
    {
        $this->changePriority("low");
    }

    public function changeToOrdinaryPriority(): void
    {
        $this->changePriority("ordinary");
    }

    public function changeToHighPriority(): void
    {
        $this->changePriority("high");
    }

    public function changeToHighestPriority(): void
    {
        $this->changePriority("highest");
    }

    private function changePriority(string $code): void
    {
        $this->code = $code;
        $this->title = self::PRIORITY_TITLES[$this->code];
    }
}
