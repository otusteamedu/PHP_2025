<?php

declare(strict_types=1);

class Solution {

    /**
     * @param Integer[] $nums
     * @return Integer[]
     */
    
    private array $hash = [];
    public function frequencySort(array $nums): array
    {
          foreach ($nums as $num) {
            $this->hash[$num]++ ;
        }

        $this->arraySort();

        return $this->getAnswer();
    }
    public function arraySort(): void
    {
        uksort($this->hash, function ($a, $b) {
            if ($this->hash[$a] < $this->hash[$b]) {
                return -1;
            }

            if ($this->hash[$a] > $this->hash[$b]) {
                return 1;
            }

            if ($a > $b) {
                return -1;
            }

            if ($a < $b) {
                return 1;
            }

            return 0;
        });
    }

    public function getAnswer(): array
    {
        $result = [];
        foreach ($this->hash as $key => $count) {
            for ($i = 0; $i < $count; $i++) {
                $result[] = $key;
            }
        }
        return $result;
    }
}