<?php

declare(strict_types=1);

namespace Two;

class Solution
{
    /**
     * @param int $numerator
     * @param int $denominator
     *
     * @return string
     */
    public function handle(int $numerator, int $denominator): string
    {
        $result = $numerator / $denominator;

        if (is_float($result)) {
            [
                $total,
                $period,
            ] = explode('.', (string)$result);

            $result = $total . '.(' . $this->period($period) . ')';
        }

        return (string)$result;
    }

    /**
     * @param string $period
     *
     * @return string
     */
    protected function period(string $period): string
    {
        $length = 0;

        $split = str_split($period);
        $splitCount = count($split);

        do {
            $length++;
            $found = true;

            $chunk = array_chunk($split, $length);

            $standard = $chunk[0];
            $chunkCount = count($chunk);

            for ($i = 1; $i < $chunkCount; $i++) {
                $probe = $chunk[$i];

                if ($probe !== $standard) {
                    $found = false;

                    break;
                }
            }
        } while ($found === false && $length < $splitCount);

        return implode('', $standard);
    }
}

echo new Solution()->handle(4, 333), PHP_EOL;
