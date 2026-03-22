<?php

declare(strict_types=1);

namespace Otus\Code\Application\Cuisine\Template;

final class CookingResult
{
    /**
     * @param string[] $log
     */
    public function __construct(
        private readonly bool $passedQualityCheck,
        private readonly string $finalState,
        private readonly array $log,
    ) {
    }

    public function passedQualityCheck(): bool
    {
        return $this->passedQualityCheck;
    }

    public function getFinalState(): string
    {
        return $this->finalState;
    }

    /**
     * @return string[]
     */
    public function getLog(): array
    {
        return $this->log;
    }
}
