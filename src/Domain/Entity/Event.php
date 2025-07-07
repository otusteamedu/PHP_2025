<?php

namespace Domain\Entity;

use DateTime;

/**
 * @OA\Schema()
 */
class Event extends Entity
{
    /** @OA\Property() */
    private string $type;
    /** @OA\Property() */
    private string $title;
    /** @OA\Property() */
    private string $priority;
    /** @OA\Property() */
    private string $comment;
    /** @OA\Property() */
    private ?DateTime $createdAt;
    /** @OA\Property() */
    private ?DateTime $updatedAt;

    public function __construct(
        ?int $id,
        string $type,
        string $title,
        string $priority,
        ?string $comment,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    ) {
        parent::__construct($id);
        $this->type = $type;
        $this->title = $title;
        $this->priority = $priority;
        $this->comment = $comment;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getType(): string {
        return $this->type;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getPriority(): string {
        return $this->priority;
    }

    public function getComment(): ?string {
        return $this->comment;
    }

    public function getCreatedAt(): ?DateTime {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTime {
        return $this->updatedAt;
    }
}