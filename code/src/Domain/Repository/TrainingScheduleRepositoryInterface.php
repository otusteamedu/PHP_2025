<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\TrainingSchedule;

interface TrainingScheduleRepositoryInterface
{
    /**
     * @return TrainingSchedule[]
     */
    public function findByDate(\DateTimeImmutable $date): array;
}
