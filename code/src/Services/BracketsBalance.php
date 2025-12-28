<?php

namespace Ak\Hw\Services;
class BracketsBalance
{
    /**
     * @param $string
     * @return string[]
     */
    public function __invoke($string):array
    {
        $result = $this->check_balance($string);
        if(!$result){
            throw  new \RuntimeException("Строка '{$string}' НЕ валидна!",200 );
        }
        return ['message' => 'Всё хорошо'];
    }

    /**
     * @param $string
     * @return bool
     */
    protected function check_balance($string): bool
    {
        $balance = 0;
        for ($i = 0, $iMax = strlen($string); $i < $iMax; $i++) {
            $char = $string[$i];

            if ($char === '(') {
                $balance++;
            } elseif ($char === ')') {
                $balance--;
            }

            if ($balance < 0) {
                return false;
            }
        }

        return $balance === 0;
    }
}