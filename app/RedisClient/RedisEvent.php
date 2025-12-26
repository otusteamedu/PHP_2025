<?php

declare(strict_types=1);

namespace App\RedisClient;

class RedisEvent
{
    private RedisConnect $client;

    public function __construct(RedisConnect $client)
    {
        $this->client = $client;
    }

    public function create($priority, array $event): void
    {
        $value = json_encode($event);

        $this->client->connect()->zadd('events', [$value => $priority]);
    }

    public function delete(): void
    {
        $this->client->connect()->del(['events']);
    }

    public function search(array $event): string
    {
        $element = $this->findElement($event);

        if ($element === null) {
            echo 'Событие не найдено';
            exit();
        }

        $value = $this->client->connect()->zrange('events', $element, $element);

        return $value[0];

    }

    private function findElement(array $event): ?int
    {
        $result = [];

        $count = count($event);

        $value = json_encode($event);

        $elements = $this->client->connect()->zrank('events', $value);

        if ($elements !== null) {
            $result[] = $elements;
        }

        if ($count <= 1) {
            return $result[0] ?? null;
        }

        foreach ($event as $key => $item) {
            $singleValue = json_encode([$key => $item]);
            $element = $this->client->connect()->zrank('events', $singleValue);

            if ($element !== null) {
                $result[] = $element;
            }
        }

        return end($result) ?? null;
    }

}