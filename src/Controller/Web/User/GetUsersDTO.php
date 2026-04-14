<?php

namespace App\Controller\Web\User;

readonly class GetUsersDTO
{
    public function __construct(
        public int $lastId = 0,
        public int $limit = 10,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (isset($data['lastId']) && isset($data['limit'])) {
            $lastId = $data['lastId'];
            $limit = $data['limit'];
            if (is_numeric($lastId) && is_numeric($limit)) {
                return new self(
                    (int) $lastId,
                    (int) $limit,
                );
            }
        }

        throw new \InvalidArgumentException('Неверное тело запроса.', 400);
    }
}
