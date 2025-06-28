<?php

declare(strict_types=1);

namespace User\Php2025;

class Validate
{
    public function validateString(?array $input): array
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->respond(405, 'Метод не поддерживается');
        }

        if (!isset($input['string']) || trim($input['string']) === '') {
            return $this->respond(422, 'Поле string не должно быть пустым');
        }

        $value = $input['string'];

        if (!$this->hasBalancedBrackets($value)) {
            return $this->respond(400, 'Скобки не сбалансированы');
        }

        return $this->respond(200, 'Строка валидна и сбалансирована');
    }

    private function hasBalancedBrackets(string $str): bool
    {
        $balance = 0;

        for ($i = 0, $len = mb_strlen($str); $i < $len; $i++) {
            $char = mb_substr($str, $i, 1);

            if ($char === '(') {
                $balance++;
            } elseif ($char === ')') {
                $balance--;
                if ($balance < 0) return false;
            }
        }

        return $balance === 0;
    }

    private function respond(int $code, string $message): array
    {
        http_response_code($code);
        header('Content-Type: application/json');
        return ['status' => $code, 'message' => $message];
    }
}