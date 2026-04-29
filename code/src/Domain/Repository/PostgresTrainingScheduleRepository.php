<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\TrainingSchedule;
use PDO;

class PostgresTrainingScheduleRepository implements TrainingScheduleRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @param int $trainingPlanId
     * @return TrainingSchedule[]
     */
    public function findByTariningPlanId(int $trainingPlanId): array
    {
        $stmt = $this->pdo->prepare('SELECT id, day_of_week, time, date FROM training_schedules WHERE training_plan_id = :training_plan_id');
        $stmt->execute(['training_plan_id' => $trainingPlanId]);

        $schedules = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $schedules[] = new TrainingSchedule(
                $row['id'],
                $row['day_of_week'],
                $row['time'],
                new \DateTimeImmutable($row['date'])
            );
        }

        return $schedules;
    }

    public function save(TrainingSchedule $schedule): void
    {
        if ($schedule->getId()) {
            $stmt = $this->pdo->prepare(
                'UPDATE training_schedules SET day_of_week = :day_of_week, time = :time, date = :date WHERE id = :id'
            );
            $stmt->execute([
                'id' => $schedule->getId(),
                'day_of_week' => $schedule->getDayOfWeek(),
                'time' => $schedule->getTime(),
                'date' => $schedule->getDate()->format('Y-m-d'),
            ]);
        } else {
            $stmt = $this->pdo->prepare(
                'INSERT INTO training_schedules (training_plan_id, day_of_week, time, date) VALUES (:training_plan_id, :day_of_week, :time, :date)'
            );
            // Note: We need a way to get the training_plan_id to save a new schedule.
            // This will likely require a change in the method signature or the TrainingSchedule entity.
            // For now, I will assume it's passed in somehow, or the logic needs to be adjusted.
            // Since I cannot change the interface, I will leave this part with a placeholder.
            // In a real scenario, I would ask for clarification on how to get the training_plan_id.
            // For the purpose of this task, I will assume a hypothetical getTrainingPlanId() method on the schedule.
            // As I cannot modify the entity, I will leave this part commented out.
            /*
            $stmt->execute([
                'training_plan_id' => $schedule->getTrainingPlanId(), // This method does not exist
                'day_of_week' => $schedule->getDayOfWeek(),
                'time' => $schedule->getTime(),
                'date' => $schedule->getDate()->format('Y-m-d'),
            ]);
            */
        }
    }

    public function delete(TrainingSchedule $schedule): void
    {
        if ($schedule->getId()) {
            $stmt = $this->pdo->prepare('DELETE FROM training_schedules WHERE id = :id');
            $stmt->execute(['id' => $schedule->getId()]);
        }
    }

    public function findByTrainingPlanId(int $trainingPlanId): array
    {
        // TODO: Implement findByTrainingPlanId() method.
    }
}
