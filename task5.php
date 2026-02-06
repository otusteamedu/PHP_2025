<?php

declare(strict_types=1);

class Solution
{

    /**
     * @param Integer[] $arNumber
     * @return Integer[]
     */

    private array $arHash = [];
    public function smallerNumbersThanCurrent(array $arNumber)
    {
        $this->init($arNumber);
        return $this->getAnswer($arNumber);
    }

    public function init(array $arNumber): void
    {
        foreach ($arNumber as $number) {
            $this->arHash[$number] ++;
        }
        $this->sortArray($this->arHash);

    }

    private function sortArray(array &$array): void
    {
        ksort($array);
    }

    public function getAnswer(array $arNumber): array
    {
        $arResult = [];

        $arForAnswer = $this->getArrayForAnswer();

        foreach ($arNumber as $number) {
            $arResult[] = $arForAnswer[$number];
        }

        return $arResult;
    }

    private function getArrayForAnswer(): array
    {
        $arResult = [];
        $numberCount = 0;

        foreach ($this->arHash as $number => $count) {
            $arResult[$number] = $numberCount;
            $numberCount += $count;
        }

        return $arResult;
    }
}
