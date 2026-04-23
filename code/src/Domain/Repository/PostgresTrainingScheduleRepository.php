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
    public function findByTrainingPlanId(int $trainingPlanId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM training_schedules WHERE training_plan_id = :training_plan_id');
        $stmt->execute(['training_plan_id' => $trainingPlanId]);

        $schedules = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $schedules[] = new TrainingSchedule(
                $row['id'],
                $row['day_of_week'],
                $row['time']
            );
        }

        return $schedules;
    }

    public function save(TrainingSchedule $schedule): void
    {
        // Этот метод нужно будет реализовать
    }

    public function delete(TrainingSchedule $schedule): void
    {
        // Этот метод нужно будет реализовать
    }
}
