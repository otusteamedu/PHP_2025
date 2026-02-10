<?php

declare(strict_types=1);

class Solution
{

    /**
     * @param Integer[] $arNumber
     * @return Integer[]
     */

    private array $arFrequencyCounter;
    private array $arPrefixSum = [];
    public function smallerNumbersThanCurrent(array $arNumber)
    {
        $this->init($arNumber);
        return $this->getAnswer($arNumber);
    }

    private function init(array $arNumber): void
    {
        $this->arFrequencyCounter = array_fill(0, 100, 0);

        foreach ($arNumber as $number) {
            $this->arFrequencyCounter[$number] ++;
        }

        $this->initPrefixSum();
    }

    private function initPrefixSum(): void
    {
        $sum = 0;
        foreach ($this->arFrequencyCounter as $number => $frequency) {
            $this->arPrefixSum[$number] = $sum;
            $sum += $this->arFrequencyCounter[$number];
        }
    }

    private function getAnswer(array $arNumber): array
    {
        $arResult = [];

        foreach ($arNumber as $number) {
            $arResult[] = $this->arPrefixSum[$number];
        }

        return $arResult;
    }
}

//var_dump((new Solution())->smallerNumbersThanCurrent([8,1,2,2,3]));