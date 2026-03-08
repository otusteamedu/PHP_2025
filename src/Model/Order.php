<?php

namespace Igor\Test\Model;

use Igor\Test\LazyLoader\LazyLoaderInterface;

/**
 * Row Data Gateway для таблицы orders
 * Каждый объект представляет одну строку таблицы
 */
class Order
{
    private ?int $id = null;
    private int $userId;
    private float $total;
    private string $status;
    private ?string $createdAt = null;
    private bool $isNew = true;
    private bool $isDirty = false;

    /**
     * Lazy loader для связанного пользователя
     */
    private ?LazyLoaderInterface $userLazyLoader = null;
    private ?User $user = null;
    private bool $userLoaded = false;

    /**
     * Конструктор
     *
     * @param array $data Данные из БД
     */
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->hydrate($data);
            $this->isNew = false;
        }
    }

    /**
     * Заполнение объекта данными
     *
     * @param array $data
     */
    public function hydrate(array $data): void
    {
        if (isset($data['id'])) {
            $this->id = (int)$data['id'];
        }
        if (isset($data['user_id'])) {
            $this->userId = (int)$data['user_id'];
        }
        if (isset($data['total'])) {
            $this->total = (float)$data['total'];
        }
        if (isset($data['status'])) {
            $this->status = $data['status'];
        }
        if (isset($data['created_at'])) {
            $this->createdAt = $data['created_at'];
        }
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
            'user_id' => $this->userId,
            'total' => $this->total,
            'status' => $this->status,
            'created_at' => $this->createdAt,
        ];
    }

    // Геттеры

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    // Сеттеры

    public function setUserId(int $userId): void
    {
        if ($this->userId !== $userId) {
            $this->userId = $userId;
            $this->isDirty = true;
            // Сбрасываем загруженного пользователя при изменении user_id
            $this->user = null;
            $this->userLoaded = false;
        }
    }

    public function setTotal(float $total): void
    {
        if ($this->total !== $total) {
            $this->total = $total;
            $this->isDirty = true;
        }
    }

    public function setStatus(string $status): void
    {
        if ($this->status !== $status) {
            $this->status = $status;
            $this->isDirty = true;
        }
    }

    /**
     * Установка ID (только для новых записей)
     *
     * @param int $id
     */
    public function setId(int $id): void
    {
        if ($this->id === null) {
            $this->id = $id;
            $this->isNew = false;
        }
    }

    // Методы для работы с состоянием

    public function isNew(): bool
    {
        return $this->isNew;
    }

    public function isDirty(): bool
    {
        return $this->isDirty;
    }

    public function markAsSaved(): void
    {
        $this->isNew = false;
        $this->isDirty = false;
    }

    // Lazy Load для пользователя

    /**
     * Установка lazy loader для пользователя
     *
     * @param LazyLoaderInterface $loader
     */
    public function setUserLazyLoader(LazyLoaderInterface $loader): void
    {
        $this->userLazyLoader = $loader;
    }

    /**
     * Получение связанного пользователя (Lazy Load)
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        if (!$this->userLoaded && $this->userLazyLoader !== null) {
            $this->user = $this->userLazyLoader->load($this->userId);
            $this->userLoaded = true;
        }

        return $this->user;
    }

    /**
     * Проверка, загружен ли пользователь
     *
     * @return bool
     */
    public function isUserLoaded(): bool
    {
        return $this->userLoaded;
    }
}
