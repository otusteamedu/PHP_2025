<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Exercise;

interface ExerciseRepositoryInterface extends RepositoryInterface
{
    public function findById(int $id): ?Exercise;
}
