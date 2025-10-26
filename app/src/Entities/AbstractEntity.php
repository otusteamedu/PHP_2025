<?php

declare(strict_types=1);

namespace App\Entities;

/**
 * Class AbstractEntity
 * @package App\Entities
 */
abstract class AbstractEntity
{
    /**
     * @param int|null $id
     */
    public function __construct(private ?int $id)
    {
    }

    /**
     * @param array $data
     * @return static
     */
    abstract public static function create(array $data): static;

    /**
     * @return array
     */
    abstract public function getAttributes(): array;

    /**
     * @return bool
     */
    public function isNewRecord(): bool
    {
        return $this->getId() === null;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return get_class($this);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
