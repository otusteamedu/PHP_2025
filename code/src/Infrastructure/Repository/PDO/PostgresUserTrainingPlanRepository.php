<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\PDO;

use App\Domain\Entity\User;
use App\Domain\Repository;
use PDO;

readonly class PostgresUserTrainingPlanRepository implements Repository\UserTrainingPlanRepositoryInterface
{
    public function __construct(
        private PDO $pdo,
        private Repository\UserRepositoryInterface $userRepository
    ) {
    }

    public function assign(int $userId, int $trainingPlanId): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO user_training_plan (user_id, training_plan_id) VALUES (:user_id, :training_plan_id)'
        );
        $stmt->execute([
            'user_id' => $userId,
            'training_plan_id' => $trainingPlanId,
        ]);
    }

    public function unassign(int $userId, int $trainingPlanId): void
    {
        $stmt = $this->pdo->prepare(
            'DELETE FROM user_training_plan WHERE user_id = :user_id AND training_plan_id = :training_plan_id'
        );
        $stmt->execute([
            'user_id' => $userId,
            'training_plan_id' => $trainingPlanId,
        ]);
    }

    public function isAssigned(int $userId, int $trainingPlanId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM user_training_plan WHERE user_id = :user_id AND training_plan_id = :training_plan_id'
        );
        $stmt->execute([
            'user_id' => $userId,
            'training_plan_id' => $trainingPlanId,
        ]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * @param int $trainingPlanId
     * @return User[]
     */
    public function findByTrainingPlanId(int $trainingPlanId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT u.*
            FROM users u
            JOIN user_training_plan utp ON u.id = utp.user_id
            WHERE utp.training_plan_id = :training_plan_id
        ');
        $stmt->execute(['training_plan_id' => $trainingPlanId]);

        $users = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $users[] = $this->userRepository->findById($row['id']);
        }

        return array_filter($users);
    }
}
