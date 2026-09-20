<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

final readonly class Event
{
    /**
     * @param array<string, string> $conditions
     * @param array<string, mixed> $payload
     */
    private function __construct(
        public string $id,
        public int $priority,
        public array $conditions,
        public array $payload,
    ) {
    }

    /**
     * @param array<string, scalar> $conditions
     * @param array<string, mixed> $payload
     */
    public static function create(int $priority, array $conditions, array $payload): self
    {
        return new self(
            bin2hex(random_bytes(16)),
            $priority,
            self::normalizeConditions($conditions),
            $payload,
        );
    }

    /**
     * @param array{id: string, priority: int, conditions: array<string, scalar>, payload: array<string, mixed>} $data
     */
    public static function fromArray(array $data): self
    {
        if (!isset($data['id'], $data['priority'], $data['conditions'], $data['payload'])
            || !is_array($data['conditions'])
            || !is_array($data['payload'])) {
            throw new InvalidArgumentException('Недопустимый формат события.');
        }

        return new self(
            (string) $data['id'],
            (int) $data['priority'],
            self::normalizeConditions($data['conditions']),
            $data['payload'],
        );
    }

    /** @return array{id: string, priority: int, conditions: array<string, string>, payload: array<string, mixed>} */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'priority' => $this->priority,
            'conditions' => $this->conditions,
            'payload' => $this->payload,
        ];
    }

    /** @param array<string, scalar> $conditions @return array<string, string> */
    private static function normalizeConditions(array $conditions): array
    {
        $normalized = [];

        foreach ($conditions as $name => $value) {
            if (!is_string($name) || $name === '') {
                throw new InvalidArgumentException('Имя условия должно быть непустой строкой.');
            }

            if (!is_scalar($value)) {
                throw new InvalidArgumentException(sprintf('Значение условия "%s" должно быть скалярным.', $name));
            }

            $normalized[$name] = match (true) {
                is_bool($value) => $value ? 'true' : 'false',
                default => (string) $value,
            };
        }

        ksort($normalized, SORT_STRING);

        return $normalized;
    }
}
