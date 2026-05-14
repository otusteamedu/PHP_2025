<?php

namespace App\Entity;

/**
 * Entity-класс пользователя.
 *
 * Класс хранит данные строки таблицы users и не содержит логики работы с БД.
 */
class User
{
    private int $id;

    private string $name;

    private ?int $phone;

    public function __construct(int $id, string $name, ?int $phone)
    {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(?int $phone): void
    {
        $this->phone = $phone;
    }
}
