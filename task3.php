<?php

declare(strict_types=1);

class Solution
{

    /**
     * @param Integer[] $arNumber1
     * @param Integer[] $arNumber2
     * @return Integer[]
     */
    public function intersection(array $arNumber1, array $arNumber2)
    {
        $numsUnique1 = $this->getArrayOfUniqueElements($arNumber1);
        $numsUnique2 = $this->getArrayOfUniqueElements($arNumber2);

        return $this->getAnswer($numsUnique1, $numsUnique2);
    }

    public function getArrayOfUniqueElements(array $arNumber): array
    {
        $arResult = [];

        foreach ($arNumber as $number) {
            $arResult[$number] = $number; ;
        }

        return $arResult;
    }

    public function getAnswer(array $arNumber1, array $arNumber2): array
    {
        $arResult = [];

        foreach ($arNumber1 as $number) {
            if (isset($arNumber2[$number])) {
                $arResult[] = $number;
            }
        }

        return $arResult;
    }
}