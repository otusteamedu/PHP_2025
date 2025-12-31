<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

final readonly class Command
{
    public function __construct(public Connect $redis)
    {
    }

    public function run(array $options)
    {
        $action = $options['action'] ?? null;

        switch ($action) {
            case 'get':
                $this->printResult($this->get($options));
                break;

            case 'create':
                $this->create($options);
                echo "Событие добавлено\n";
                break;

            case 'delete':
                $this->delete();
                echo "Все события удалены\n";
                break;

            default:
                throw new RuntimeException("Неизвестное событие: $action");
        }
    }

    private function get(array $options): array
    {
        $redis = $this->redis->connect();

        $conditions = $options['conditions'] ?? null;
        if (!$conditions) {
            throw new RuntimeException("Для get нужно указать --conditions");
        }

        $members = $redis->zrange('events', 0, -1);

        $result = [];
        foreach ($members as $member) {
            $ok = true;

            foreach (explode(',', $conditions) as $cond) {
                if (!str_contains($member, $cond)) {
                    $ok = false;
                    break;
                }
            }

            if ($ok) {
                $result[$member] = $redis->zscore('events', $member);
            }
        }

        return $result;
    }


    private function create(array $options): void
    {
        $redis = $this->redis->connect();

        if (!isset($options['priority'])) {
            throw new RuntimeException("Для create нужно указать --priority");
        }

        $score = (int)$options['priority'];
        $member = $options['conditions'] ?? "";

        $redis->zadd('events', [$member => $score]);
    }

    private function delete(): void
    {
        $this->redis->connect()->del('events');
    }

    private function printResult(array $result): void
    {
        if (empty($result)) {
            throw new RuntimeException("Ничего не найдено");
        }

        echo str_pad("Member", 30) . " | " . str_pad("Score", 10) . "\n";
        echo str_repeat("-", 45) . "\n";

        foreach ($result as $member => $score) {
            echo str_pad($member, 30) . " | " . str_pad((string)$score, 10) . "\n";
        }
    }
}
