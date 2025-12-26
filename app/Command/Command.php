<?php

declare(strict_types=1);

namespace App\Command;


use App\RedisClient\RedisEvent;
use App\RedisClient\RedisConnect;

class Command
{
    public function run(string $action, ?string $priority, ?string $param1, ?string $param2): string
    {
        $redisConnect = new RedisConnect();

        if ($action === 'create') {
            $this->validateForSearchParam($param1, $param2);

            $payload = $this->buildPayload($param1, $param2);

            $redisClient = new RedisEvent($redisConnect);

            $redisClient->create($priority, $payload);
        }

        if ($action === 'delete') {
            $redisClient = new RedisEvent($redisConnect);
            $redisClient->delete();
        }

        if ($action === 'search') {
            $this->validateForSearchParam($param1, $param2);

            $payload = $this->buildPayload($param1, $param2);

            $redisClient = new RedisEvent($redisConnect);

            return $redisClient->search($payload);
        }

        return 'ок';
    }

    private function validateForSearchParam($param1, $param2): void
    {
        if ($param1 === null && $param2 === null) {
            echo 'Один из параметров должен быть заполнен.';
            exit();
        }
    }

    private function buildPayload(?string $param1, ?string $param2): array
    {
        $conditions = [];

        if ($param1 !== null) {
            $conditions['param1'] = $param1;
        }
        if ($param2 !== null) {
            $conditions['param2'] = $param2;
        }

        return $conditions;

    }
}
