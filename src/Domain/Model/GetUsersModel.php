<?php

declare(strict_types=1);

namespace App\Domain\Model;

class GetUsersModel
{
    public const int MAX_USERS_REQUEST = 10;

    private readonly int $lastId;
    private readonly int $limit;

    public function __construct(int $lastId, int $limit)
    {
        $this->setLastId($lastId);
        $this->setLimit($limit);
    }

    public function getLastId(): int
    {
        return $this->lastId;
    }

    public function setLastId(int $lastId): void
    {
        if ($lastId < 0) {
            throw new \Exception('Последний id пользователя не может быть < 0');
        }

        $this->lastId = $lastId;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        if ($limit > self::MAX_USERS_REQUEST) {
            throw new \Exception('Максимальное количество пользователей в запросе: ' . self::MAX_USERS_REQUEST);
        }

        $this->limit = $limit;
    }
}
