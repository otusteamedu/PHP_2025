<?php

declare(strict_types=1);

namespace App\Application\ReadingTimeCalculator;

interface ReadingTimeCalculatorInterface
{
    public function calculate(string $content): int;
}
