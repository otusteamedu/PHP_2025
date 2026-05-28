<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class TrainingNotificationEvent implements \JsonSerializable
{
    private array $emails;
    private string $title;
    private string $description;
    private int $trainingScheduleId;
    private \DateTimeImmutable $sendAt;

    public function __construct(
        array $emails,
        string $title,
        string $description,
        int $trainingScheduleId,
        \DateTimeImmutable $sendAt
    ) {
        $this->emails = $emails;
        $this->title = $title;
        $this->description = $description;
        $this->trainingScheduleId = $trainingScheduleId;
        $this->sendAt = $sendAt;
    }

    public function getEmails(): array
    {
        return $this->emails;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTrainingScheduleId(): int
    {
        return $this->trainingScheduleId;
    }

    public function getSendAt(): \DateTimeImmutable
    {
        return $this->sendAt;
    }

    public function jsonSerialize(): array
    {
        return [
            'emails' => $this->getEmails(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'trainingScheduleId' => $this->getTrainingScheduleId(),
            'sendAt' => $this->getSendAt()->format(\DateTimeInterface::ISO8601),
        ];
    }
}
