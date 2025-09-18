<?php
declare(strict_types=1);

namespace App\Service;

final readonly class ParenthesesValidator
{
    public function isBalanced(string $s): bool
    {
        $balance = 0;
        $input = $this->extractParentheses($s);
        $len = mb_strlen($input);

        if (!$len) {
            return false;
        }

        for($i = 0; $i < $len; $i++) {
            $ch = $input[ $i ];
            if ($ch === '(') {
                $balance++;
            } elseif ($ch === ')') {
                $balance--;
                if ($balance < 0) {
                    return false;
                }
            }
        }

        return $balance === 0;
    }

    private function extractParentheses(string $input): string
    {
        return preg_replace('/[^()]/', '', $input);
    }
}
