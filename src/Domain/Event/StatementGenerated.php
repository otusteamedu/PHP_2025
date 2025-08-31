<?php

declare(strict_types=1);

namespace App\Domain\Event;

use App\Domain\Entity\BankStatementRequest;
use DateTimeImmutable;

final readonly class StatementGenerated
{
    public function __construct(
        private string $requestId,
        private string $telegramChatId,
        private string $statementContent,
        private DateTimeImmutable $occurredAt
    ) {
    }

    /**
     * Создает событие на основе запроса и содержимого выписки
     */
    public static function fromRequest(BankStatementRequest $request, string $statementContent): self
    {
        return new self(
            $request->id(),
            $request->telegramChatId()->value(),
            $statementContent,
            new DateTimeImmutable()
        );
    }

    /**
     * Возвращает id запроса на выписку
     */
    public function requestId(): string
    {
        return $this->requestId;
    }

    /**
     * Возвращает id Telegram чата для отправки уведомления
     */
    public function telegramChatId(): string
    {
        return $this->telegramChatId;
    }

    /**
     * Возвращает содержимое сгенерированной выписки
     */
    public function statementContent(): string
    {
        return $this->statementContent;
    }

    /**
     * Возвращает дату и время возникновения события
     */
    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
