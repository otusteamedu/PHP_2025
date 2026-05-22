<?php

declare(strict_types=1);

namespace App\Http;

use InvalidArgumentException;
use JsonException;

/**
 * Читатель JSON-тела HTTP-запроса
 */
final class JsonRequestReader
{
    /**
     * Возвращает тело запроса в виде массива
     *
     * @return array<string, mixed>
     */
    public function read(): array
    {
        $body = file_get_contents('php://input');

        try {
            $data = json_decode($body ?: '', true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new InvalidArgumentException('Некорректное JSON-тело запроса');
        }

        if (!is_array($data)) {
            throw new InvalidArgumentException('JSON-тело запроса должно быть объектом');
        }

        return $data;
    }
}
