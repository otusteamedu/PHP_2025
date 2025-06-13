<?php

namespace App\http;

use InvalidArgumentException;

class Request {
    private string $input;

    public function __construct(array $postData) {
        if (!isset($postData['emails']) || !is_string($postData['emails'])) {
            throw new InvalidArgumentException("Неверный формат: значение поля 'emails' должно быть строкой.");
        }
        $trimmedInput = trim($postData['emails']);
        if ($trimmedInput === '') {
            throw new InvalidArgumentException("Неверный формат: значение поля 'emails' пустое");
        }

        $this->input = $trimmedInput;
    }

    public function getInput(): string {
        return $this->input;
    }
}

