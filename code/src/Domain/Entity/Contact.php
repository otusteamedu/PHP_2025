<?php

declare(strict_types=1);

namespace MkdBot\Domain\Entity;

use MkdBot\Domain\Enum\ContactType;

/**
 * Сущность контакта (УК или член совета дома)
 */
class Contact
{
    public function __construct(
        private ?int $id = null,
        private ContactType $type = ContactType::Uk,
        private string $name = '',
        private string $role = '',
        private string $phone = '',
        private string $email = '',
        private string $description = '',
        private int $sort = 0,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ContactType
    {
        return $this->type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * Форматирует контакт УК для вывода в боте
     */
    public function formatUk(): string
    {
        $header = $this->role !== '' ? "🏢 {$this->name} — {$this->role}" : "🏢 {$this->name}";
        $lines = [$header];
        if ($this->phone !== '') {
            $lines[] = "Телефон: {$this->phone}";
        }
        if ($this->email !== '') {
            $lines[] = "Email: {$this->email}";
        }
        if ($this->description !== '') {
            $lines[] = "Описание: {$this->description}";
        }
        return implode("\n", $lines);
    }

    /**
     * Форматирует контакт совета дома для вывода в боте
     */
    public function formatCouncil(): string
    {
        $header = $this->role !== '' ? "👤 {$this->name} — {$this->role}" : "👤 {$this->name}";
        $lines = [$header];
        if ($this->phone !== '') {
            $lines[] = "Телефон: {$this->phone}";
        }
        if ($this->email !== '') {
            $lines[] = "Email: {$this->email}";
        }
        if ($this->description !== '') {
            $lines[] = "Описание: {$this->description}";
        }
        return implode("\n", $lines);
    }
}
