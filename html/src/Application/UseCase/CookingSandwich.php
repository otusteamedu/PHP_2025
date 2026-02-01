<?php

declare(strict_types=1);

namespace Otus\Food\Application\UseCase;

use Otus\Food\Application\Process\ProcessInterface;
use Otus\Food\Domain\Kitchen\Entity\Meal;

final readonly class CookingSandwich
{
    /**
     * @param ProcessInterface $process
     */
    public function __construct(private ProcessInterface $process)
    {
    }

    /**
     * @return Meal
     */
    public function cooking(): Meal
    {
        return $this->process->run();
    }
}
