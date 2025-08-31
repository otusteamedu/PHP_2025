<?php

declare(strict_types=1);

namespace App\Application\DTO;

use App\Domain\ValueObject\DateRange;
use App\Domain\ValueObject\TelegramChatId;
use DateTimeImmutable;

final readonly class CreateStatementRequestDTO
{
    public function __construct(
        private string $startDate,
        private string $endDate,
        private string $telegramChatId
    ) {
    }

    /**
     * Возвращает начальную дату периода
     */
    public function startDate(): string
    {
        return $this->startDate;
    }

    /**
     * Возвращает конечную дату периода
     */
    public function endDate(): string
    {
        return $this->endDate;
    }

    /**
     * Возвращает id Telegram чата
     */
    public function telegramChatId(): string
    {
        return $this->telegramChatId;
    }

    /**
     * Преобразует DTO в объект DateRange
     */
    public function toDateRange(): DateRange
    {
        $startDate = new DateTimeImmutable($this->startDate);
        $endDate = new DateTimeImmutable($this->endDate);

        return new DateRange($startDate, $endDate);
    }

    /**
     * Преобразует DTO в объект TelegramChatId
     */
    public function toTelegramChatId(): TelegramChatId
    {
        return new TelegramChatId($this->telegramChatId);
    }
}
