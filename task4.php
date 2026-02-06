<?php

declare(strict_types=1);

class Solution
{

    /**
     * @param Integer[] $arNumber
     * @return Integer[]
     */

    private array $arPositiveNumbers = [];
    private array $arNegativeNumbers = [];
    public function findMaxK(array $arNumber)
    {
        $this->init($arNumber);
        return $this->getAnswer();
    }

    public function init(array $arNumber): void
    {
        foreach ($arNumber as $number) {
            if ($number < 0) {
                $this->arNegativeNumbers[$number] = $number;
            } else {
                $this->arPositiveNumbers[$number] = $number;
            }
        }
    }
    public function getAnswer(): int
    {
        $maxNumber = -1;
        foreach ($this->arNegativeNumbers as $number) {
            $absNumber = abs($number);
            $isSuitableNumber = isset($this->arPositiveNumbers[$absNumber])
                                && $absNumber > $maxNumber;
            if ($isSuitableNumber) {
                $maxNumber = $absNumber;
            }
        }

        return $maxNumber;
    }
}