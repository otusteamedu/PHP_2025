<?php

namespace Igor\Test\Model;

/**
 * Модель пользователя (для демонстрации Lazy Load)
 */
class User
{
    private int $id;
    private string $name;
    private string $email;
    private ?string $createdAt = null;

    /**
     * Конструктор
     *
     * @param array $data Данные из БД
     */
    public function __construct(array $data)
    {
        $this->id = (int)$data['id'];
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->createdAt = $data['created_at'] ?? null;
    }

    /**
     * Получение данных объекта в виде массива
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->createdAt,
        ];
    }

    // Геттеры

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
}
