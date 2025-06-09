<?php

declare(strict_types=1);

namespace App\Application\ReadingTimeCalculator;

class ReadingTimeCalculator implements ReadingTimeCalculatorInterface
{
    const int AVG_READING_SPEED = 1500;

    public function calculate(string $content): int
    {
        $charactersNumber = $this->getNumberOfContentCharacters($content);

        return (int)ceil($charactersNumber / self::AVG_READING_SPEED);
    }

    public function getNumberOfContentCharacters(string $content): int
    {
        $content = preg_replace('/\s+/', ' ', strip_tags($content));

        return mb_strlen($content);
    }
}
