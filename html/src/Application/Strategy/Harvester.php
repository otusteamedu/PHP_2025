<?php

declare(strict_types=1);

namespace Otus\Food\Application\Strategy;

final readonly class Harvester
{
    /**
     * @param array $strategies
     */
    public function __construct(
        private array $strategies,
    ) {
    }

    /**
     * @param string $title
     *
     * @return MealStrategy
     *
     * @throws HarvesterException
     */
    public function getStrategy(string $title): MealStrategy
    {
        return $this->strategies[$title] ?? throw new HarvesterException($title);
    }
}
