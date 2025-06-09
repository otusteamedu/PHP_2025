<?php

declare(strict_types=1);

namespace App\Application\NewsContentModifier;

use App\Application\ReadingTimeCalculator\ReadingTimeCalculatorInterface;

readonly class ReadingTimeNewsContentModifier implements NewsContentModifierInterface
{
    public function __construct(
        private NewsContentModifierInterface $newsContentModifier,
        private ReadingTimeCalculatorInterface $readingTimeCalculator
    )
    {
    }

    public function modify(string $content): string
    {
        $content = $this->newsContentModifier->modify($content);

        return $content . $this->renderReadingTime($content);
    }

    private function renderReadingTime(string $content): string
    {
        $readingTime = $this->readingTimeCalculator->calculate($content);

        return "<p><b>Reading time:</b> $readingTime min.</p>";
    }
}
