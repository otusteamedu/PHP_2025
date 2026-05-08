<?php

declare(strict_types=1);

namespace App\Application\Console;

use InvalidArgumentException;
use JsonException;

/**
 * Обертка для чтения и проверки аргументов командной строки
 */
final class InputArguments
{
    /**
     * @param array<string, string> $options
     */
    public function __construct(private readonly array $options)
    {
    }

    /**
     * Возвращает обязательную опцию как целое число
     */
    public function getRequiredInt(string $name): int
    {
        $value = $this->getRequiredOption($name);

        if (!preg_match('/^-?\d+$/', $value)) {
            throw new InvalidArgumentException('Option --' . $name . ' must be an integer.');
        }

        return (int) $value;
    }

    /**
     * Возвращает обязательную опцию как JSON-объект
     *
     * @return array<string, mixed>
     */
    public function getRequiredJsonObject(string $name): array
    {
        $value = $this->getRequiredOption($name);

        try {
            $decoded = json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('Option --' . $name . ' must be valid JSON.', 0, $exception);
        }

        if (!is_array($decoded) || array_is_list($decoded)) {
            throw new InvalidArgumentException('Option --' . $name . ' must be a JSON object.');
        }

        return $decoded;
    }

    /**
     * Возвращает обязательную строковую опцию
     */
    private function getRequiredOption(string $name): string
    {
        if (!array_key_exists($name, $this->options)) {
            throw new InvalidArgumentException('Missing required option --' . $name . '.');
        }

        return $this->options[$name];
    }
}
