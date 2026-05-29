<?php

declare(strict_types=1);

namespace MkdBot\Domain\Entity;

use DateTimeImmutable;
use MkdBot\Domain\Enum\ProposalType;

/**
 * Сущность предложения (новый функционал или предложение совету)
 */
class Proposal
{
    public function __construct(
        private ?int $id = null,
        private ProposalType $type = ProposalType::Feature,
        private int $userId = 0,
        private string $userName = '',
        private string $subject = '',
        private string $content = '',
        private ?DateTimeImmutable $createdAt = null,
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ProposalType
    {
        return $this->type;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Проверяет, не превышает ли тема лимит символов (200)
     */
    public function isSubjectValid(): bool
    {
        return mb_strlen($this->subject) <= 200 && mb_strlen($this->subject) > 0;
    }

    /**
     * Проверяет, не превышает ли описание лимит символов (3000)
     */
    public function isContentValid(): bool
    {
        return mb_strlen($this->content) <= 3000 && mb_strlen($this->content) > 0;
    }
}
