<?php
declare(strict_types=1);

namespace App\Model;

class Event
{
    private int $priority;

    private array $conditions;

    private array $event;

    private ?string $id;

    /**
     * @param int $priority
     * @param array $conditions
     * @param array $event
     * @param string|null $id
     */
    public function __construct(
        int $priority,
        array $conditions,
        array $event,
        ?string $id = null
    ) {
        $this->priority = $priority;
        $this->conditions = $conditions;
        $this->event = $event;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @return array
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /**
     * @return array
     */
    public function getEvent(): array
    {
        return $this->event;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return void
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'priority' => $this->priority,
            'conditions' => $this->conditions,
            'event' => $this->event
        ];
    }

    /**
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            (int)$data['priority'],
            $data['conditions'],
            $data['event'],
            $data['id'] ?? null
        );
    }
}
