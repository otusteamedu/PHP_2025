<?php

declare(strict_types=1);

namespace User\Php2025;

class Validate
{
    public function getValidateString(?array $input): array
    {
        $response = new Response();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $response->createResponse(405, 'Метод не поддерживается');
        }

        if (!isset($input['string']) || trim($input['string']) === '') {
            return $response->createResponse(422, 'Поле string не должно быть пустым');
        }

        $value = $input['string'];

        if (!$this->hasBalancedBrackets($value)) {
            return $response->createResponse(400, 'Скобки не сбалансированы');
        }

        return $response->createResponse(200, 'Ок');
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
}
