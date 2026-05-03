<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository\PDO;

use App\Domain\Entity\Exercise;
use App\Domain\Entity\TrainingPlan;
use App\Domain\Entity\TrainingPlanExercise;
use App\Domain\Entity\TrainingSchedule;
use App\Domain\Repository;

use Exception;
use PDO;

readonly class PostgresTrainingPlanRepository implements Repository\TrainingPlanRepositoryInterface
{
    public function __construct(
        private PDO                                 $pdo,
        private Repository\TrainingScheduleRepositoryInterface $trainingScheduleRepository,
        private Repository\ExerciseRepositoryInterface         $exerciseRepository,
    ) {
    }

    /**
     * @throws Exception
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
     * @throws Exception
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
     * @throws Exception
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
     * @param object $entity
     * @return TrainingPlan
     */
    public function save(object $entity): TrainingPlan
    {
        if (!$entity instanceof TrainingPlan) {
            throw new \InvalidArgumentException('Entity must be an instance of TrainingPlan.');
        }

        if ($entity->getId() !== null) {
            $stmt = $this->pdo->prepare(
                'UPDATE training_plans SET name = :name, description = :description, created_at = :created_at WHERE id = :id'
            );
            $stmt->execute($this->dehydrateTrainingPlan($entity));
        } else {
            $stmt = $this->pdo->prepare(
                'INSERT INTO training_plans (name, description, created_at) VALUES (:name, :description, :created_at) RETURNING id'
            );
            $stmt->execute($this->dehydrateTrainingPlan($entity, false));
            $id = $stmt->fetchColumn();
            $entity->setId($id);
        }

        return $entity;
    }

    /**
     * @param object $entity
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
     * @throws Exception
     */
    private function hydrateTrainingPlan(array $data): TrainingPlan
    {
        $schedules = $this->trainingScheduleRepository->findByTrainingPlanId((int)$data['id']);

        return new TrainingPlan(
            (int)$data['id'],
            $data['name'],
            $data['description'],
            new \DateTimeImmutable($data['created_at']),
            $schedules
        );
    }

    private function dehydrateTrainingPlan(TrainingPlan $plan, bool $includeId = true): array
    {
        $data = [
            'name' => $plan->getName(),
            'description' => $plan->getDescription(),
            'created_at' => $plan->getCreatedAt()->format('Y-m-d H:i:s'),
        ];

        if ($includeId) {
            $data['id'] = $plan->getId();
        }

        return $data;
    }

    /**
     * @throws Exception
     */
    public function findWithExercisesByDay(int $trainingPlanId): ?TrainingPlan
    {
        $planStmt = $this->pdo->prepare('SELECT * FROM training_plans WHERE id = :id');
        $planStmt->execute(['id' => $trainingPlanId]);
        $planData = $planStmt->fetch(PDO::FETCH_ASSOC);

        if (!$planData) {
            return null;
        }

        $schedulesStmt = $this->pdo->prepare('SELECT id, day_of_week, time, date FROM training_schedules WHERE training_plan_id = :plan_id ORDER BY date ASC');
        $schedulesStmt->execute(['plan_id' => $trainingPlanId]);
        $schedulesData = $schedulesStmt->fetchAll(PDO::FETCH_ASSOC);

        $schedules = [];
        foreach ($schedulesData as $scheduleData) {
            $scheduleId = (int)$scheduleData['id'];
            $scheduleData['time'] = new \DateTimeImmutable($scheduleData['time']);

            $exercisesStmt = $this->pdo->prepare('
                SELECT
                    tpe.id as tpe_id,
                    tpe.sequence,
                    tpe.repetitions,
                    tpe.duration,
                    tpe.cycle,
                    e.id as exercise_id,
                    e.title as exercise_name,
                    e.description as exercise_description
                FROM training_plan_exercises tpe
                JOIN exercises e ON tpe.exercise_id = e.id
                WHERE tpe.schedule_id = :schedule_id
                ORDER BY tpe.sequence
            ');
            $exercisesStmt->execute(['schedule_id' => $scheduleId]);
            $exercisesData = $exercisesStmt->fetchAll(PDO::FETCH_ASSOC);

            $exercises = [];
            foreach ($exercisesData as $exerciseData) {
                $exercise = new Exercise(
                    (int)$exerciseData['exercise_id'],
                    $exerciseData['exercise_name'],
                    $exerciseData['exercise_description']
                );
                $exercises[] = new TrainingPlanExercise(
                    (int)$exerciseData['tpe_id'],
                    $exercise,
                    $exerciseData['sequence'],
                    (int)$exerciseData['repetitions'],
                    (int)$exerciseData['duration'],
                    (int)$exerciseData['cycle']
                );
            }

            $schedules[] = new TrainingSchedule(
                $scheduleId,
                (int)$scheduleData['day_of_week'],
                $scheduleData['time'],
                new \DateTimeImmutable($scheduleData['date']),
                $exercises
            );
        }

        return new TrainingPlan(
            (int)$planData['id'],
            $planData['name'],
            $planData['description'],
            new \DateTimeImmutable($planData['created_at']),
            $schedules
        );
    }
}
