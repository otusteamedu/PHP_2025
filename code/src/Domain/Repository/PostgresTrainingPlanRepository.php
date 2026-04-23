<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\TrainingPlan;
use PDO;

class PostgresTrainingPlanRepository implements TrainingPlanRepositoryInterface
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly TrainingScheduleRepositoryInterface $trainingScheduleRepository
    ) {
    }

    /**
     * @throws \Exception
     */
    public function findById(int $id): ?TrainingPlan
    {
        $stmt = $this->pdo->prepare('SELECT * FROM training_plans WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->hydrateTrainingPlan($data);
    }

    /**
     * @param int $userId
     * @return TrainingPlan[]
     * @throws \Exception
     */
    public function findByUserId(int $userId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT tp.*
            FROM training_plans tp
            JOIN user_training_plan utp ON tp.id = utp.training_plan_id
            WHERE utp.user_id = :user_id
            ORDER BY tp.created_at DESC
        ');
        $stmt->execute(['user_id' => $userId]);
        $plansData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $plans = [];

        foreach ($plansData as $data) {
            $plans[] = $this->hydrateTrainingPlan($data);
        }

        return $plans;
    }

    /**
     * @param int $page
     * @param int $limit
     * @return array
     * @throws \Exception
     */
    public function findAll(int $page = 1, int $limit = 10): array
    {
        $totalStmt = $this->pdo->query('SELECT COUNT(*) FROM training_plans');
        $total = (int) $totalStmt->fetchColumn();

        $offset = ($page - 1) * $limit;
        $stmt = $this->pdo->prepare('SELECT * FROM training_plans ORDER BY id LIMIT :limit OFFSET :offset');
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $plansData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $plans = [];

        foreach ($plansData as $data) {
            $plans[] = $this->hydrateTrainingPlan($data);
        }

        return [
            'total' => $total,
            'plans' => $plans,
        ];
    }

    /**
     * @param TrainingPlan $entity
     */
    public function save(object $entity): TrainingPlan
    {
        if (!$entity instanceof TrainingPlan) {
            throw new \InvalidArgumentException('Entity must be an instance of TrainingPlan.');
        }

        if ($entity->getId() !== null) {
            // Update existing plan
            $stmt = $this->pdo->prepare(
                'UPDATE training_plans SET name = :name, status = :status, description = :description, created_at = :created_at WHERE id = :id'
            );
            $stmt->execute($this->dehydrateTrainingPlan($entity));
        } else {
            // Insert new plan
            $stmt = $this->pdo->prepare(
                'INSERT INTO training_plans (name, status, description, created_at) VALUES (:name, :status, :description, :created_at) RETURNING id'
            );
            $stmt->execute($this->dehydrateTrainingPlan($entity, false));
            $id = $stmt->fetchColumn();
            $entity->setId($id);
        }

        return $entity;
    }

    /**
     * @param TrainingPlan $entity
     */
    public function remove(object $entity): void
    {
        if (!$entity instanceof TrainingPlan || $entity->getId() === null) {
            throw new \InvalidArgumentException('Entity must be an instance of TrainingPlan with a valid ID.');
        }

        $stmt = $this->pdo->prepare('DELETE FROM training_plans WHERE id = :id');
        $stmt->execute(['id' => $entity->getId()]);
    }

    /**
     * @throws \Exception
     */
    private function hydrateTrainingPlan(array $data): TrainingPlan
    {
        $schedules = $this->trainingScheduleRepository->findByTrainingPlanId((int)$data['id']);

        return new TrainingPlan(
            (int)$data['id'],
            $data['name'],
            $data['status'],
            $data['description'],
            new \DateTimeImmutable($data['created_at']),
            [],
            $schedules
        );
    }

    private function dehydrateTrainingPlan(TrainingPlan $plan, bool $includeId = true): array
    {
        $data = [
            'name' => $plan->getName(),
            'status' => $plan->getStatus(),
            'description' => $plan->getDescription(),
            'created_at' => $plan->getCreatedAt()->format('Y-m-d H:i:s'),
        ];

        if ($includeId) {
            $data['id'] = $plan->getId();
        }

        return $data;
    }
}
