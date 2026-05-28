<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\PDO;

use App\Application\DTO\PaginationDTO;
use App\Domain\Entity\Exercise;
use App\Domain\Repository\ExerciseRepositoryInterface;
use PDO;

readonly class PostgresExerciseRepository implements ExerciseRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findById(int $id): ?Exercise
    {
        $stmt = $this->pdo->prepare('SELECT * FROM exercises WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return new Exercise(
            (int)$data['id'],
            $data['name'],
            $data['description']
        );
    }

    public function findAll(int $page = 1, int $limit = 10): PaginationDTO
    {
        return new PaginationDTO([], 0, $page, $limit);
    }

    public function save(object $entity): object
    {
        // Not implemented for this use case
        return $entity;
    }

    public function remove(object $entity): void
    {
        // Not implemented for this use case
    }
}
