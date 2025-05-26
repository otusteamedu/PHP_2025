<?php
namespace App;

class Validation
{
    static public function checkMethod(string $method): void
    {
        if ($method !== 'POST') {
            throw new \Exception('Method Not Allowed' . PHP_EOL);
        }
    }

    static public function checkNotEmptyString(string $str): void
    {
        if (empty($str)) {
            throw new \Exception('Error: String is empty' . PHP_EOL);
        }
    }

    static public function checkCorrectString(string $str): void
    {
        $result = self::isBalanced($str);
        if (!$result) {
            throw new \Exception('Error: String is not balanced' . PHP_EOL);
        }
    }

    static protected function isBalanced(string $str): bool {
        $balance = 0;
        for ($i = 0; $i < strlen($str); $i++) {
            if ($str[$i] === '(') {
                $balance++;
            } elseif ($str[$i] === ')') {
                $balance--;
                if ($balance < 0) {
                    return false;
                }
            }
        }
        return $balance === 0;
    }
}