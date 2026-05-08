<?php

declare(strict_types=1);

namespace App\Infrastructure\Redis;

use InvalidArgumentException;

/**
 * Строит Redis-ключи для индекса событий по условиям
 */
final class RedisConditionKeyBuilder
{
    private const CONDITIONS_KEY_PREFIX = 'events:conditions:';

    /**
     * Формирует ключ из полного набора условий события
     *
     * Условия сортируются по имени параметра, чтобы одинаковые наборы условий всегда давали одинаковый ключ
     *
     * Примеры:
     * ['param1' => 1] => events:conditions:param1=1
     * ['param2' => 2, 'param1' => 1] => events:conditions:param1=1|param2=2
     *
     * Такие составные ключи нужны, чтобы при поиске быстро находить события с подходящим набором условий
     *
     * @param array<string, mixed> $conditions
     */
    public function buildKey(array $conditions): string
    {
        ksort($conditions);

        $parts = [];

        foreach ($conditions as $param => $value) {
            $stringValue = $this->convertValueToString($value);

            $this->validateKeyPart($param, 'condition name');
            $this->validateKeyPart($stringValue, 'condition value');

            $parts[] = $param . '=' . $stringValue;
        }

        return self::CONDITIONS_KEY_PREFIX . implode('|', $parts);
    }

    /**
     * Строит ключи для всех непустых подмножеств входящих параметров
     *
     * Это позволяет найти события, у которых выполнены все условия, даже если в запросе параметров больше
     *
     * @param array<string, mixed> $params
     *
     * @return string[]
     */
    public function buildKeysForAllSubsets(array $params): array
    {
        ksort($params);

        $paramNames = array_keys($params);
        $paramsCount = count($paramNames);
        $keys = [];

        for ($mask = 1; $mask < (2 ** $paramsCount); $mask++) {
            $subset = [];

            for ($position = 0; $position < $paramsCount; $position++) {
                if (($mask & (2 ** $position)) === 0) {
                    continue;
                }

                $paramName = $paramNames[$position];
                $subset[$paramName] = $params[$paramName];
            }

            $keys[] = $this->buildKey($subset);
        }

        return $keys;
    }

    private function convertValueToString(mixed $value): string
    {
        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        if ($value === null) {
            return 'null';
        }

        return (string) $value;
    }

    /**
     * Проверяет, что часть ключа не содержит разделители формата
     *
     * @param string $value Проверяемая часть ключа
     * @param string $label Название части ключа для текста ошибки
     *
     */
    private function validateKeyPart(string $value, string $label): void
    {
        if (str_contains($value, '=') || str_contains($value, '|')) {
            throw new InvalidArgumentException($label . ' cannot contain "=" or "|".');
        }
    }
}
