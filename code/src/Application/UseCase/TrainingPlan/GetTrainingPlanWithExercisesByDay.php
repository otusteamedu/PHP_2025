<?php

declare(strict_types=1);

namespace App\Application\UseCase\TrainingPlan;

use App\Application\UseCase\TrainingPlan\Dto\TrainingPlanWithExercisesByDayDto;
use App\Domain\Repository\TrainingPlanRepositoryInterface;

class GetTrainingPlanWithExercisesByDay
{
    public function __construct(
        private readonly TrainingPlanRepositoryInterface $trainingPlanRepository
    ) {
    }

    public function __invoke(int $trainingPlanId): TrainingPlanWithExercisesByDayDto
    {
        $trainingPlan = $this->trainingPlanRepository->findWithExercisesByDay($trainingPlanId);

        $days = [];
        foreach ($trainingPlan->getSchedules() as $schedule) {
            $dateKey = $schedule->getDate()->format('Y-m-d');
            $exercises = [];
            foreach ($schedule->getExercises() as $exercise) {
                $exercises[] = [
                    'id' => $exercise->getExercise()->getId(),
                    'name' => $exercise->getExercise()->getTitle(),
                    'sequence' => $exercise->getSequence(),
                    'repetitions' => $exercise->getRepetitions(),
                    'duration' => $exercise->getDuration(),
                    'cycle' => $exercise->getCycle(),
                ];
            }

            $days[$dateKey] = [
                'date' => $dateKey,
                'time' => $schedule->getStartTime()->format('H:i'),
                'day' => $schedule->getDate()->format('Y-m-d'),
                'exercises' => $exercises,
            ];
        }

        return new TrainingPlanWithExercisesByDayDto(
            $trainingPlan->getId(),
            $trainingPlan->getName(),
            $trainingPlan->getDescription(),
            array_values($days)
        );
    }
}
