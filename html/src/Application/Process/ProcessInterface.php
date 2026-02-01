<?php

declare(strict_types=1);

namespace Otus\Food\Application\Process;

use Otus\Food\Domain\Kitchen\Entity\Meal;

interface ProcessInterface
{
    /**
     * @return Meal
     */
    public function run(): Meal;
}
