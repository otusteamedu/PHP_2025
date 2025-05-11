<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Entities;

abstract class AbstractEntity
{

    public function __construct(private ?int $id)
    {
    }

    abstract public static function create(array $data): static;

    abstract public function getAttributes(): array;

    public function isNewRecord(): bool
    {
        return $this->getId() === null;
    }

    public function getClassName(): string
    {
        return get_class($this);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
