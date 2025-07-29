<?

declare(strict_types=1);

namespace Kamalo\EventsService\Domain\Entity;

class Event
{
    public function __construct(
        private ?int $id,
        private int $priority,
        private array $conditions
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function jsonSerialize(): array
    {
        $data = [
            'id' => $this->id,
            'priority' => $this->priority,
        ];

        foreach ($this->conditions as $key => $value) {
            $data["conditions:$key"] = $value;
        }

        return $data;
    }
}
