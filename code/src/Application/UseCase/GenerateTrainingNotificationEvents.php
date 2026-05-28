<?php

declare(strict_types=1);

namespace App\Application\UseCase;

error_reporting(E_ALL & ~E_DEPRECATED);

use App\Domain\Entity\TrainingNotificationEvent;
use App\Domain\Event\EventPublisherInterface;
use App\Domain\Repository\TrainingPlanRepositoryInterface;
use App\Domain\Repository\TrainingScheduleRepositoryInterface;
use App\Domain\Repository\UserTrainingPlanRepositoryInterface;

readonly class GenerateTrainingNotificationEvents
{
    public function __construct(
        private TrainingScheduleRepositoryInterface $trainingScheduleRepository,
        private UserTrainingPlanRepositoryInterface $userTrainingPlanRepository,
        private EventPublisherInterface             $eventPublisher,
        private TrainingPlanRepositoryInterface     $trainingPlanRepository,
    ) {
    }

    public function execute(?\DateTimeImmutable $date = null): void
    {
        $date ??= new \DateTimeImmutable('today');


        $trainingSchedules = $this->trainingScheduleRepository->findByDate($date);

        foreach ($trainingSchedules as $schedule) {
            $planId = $schedule->getTrainingPlanId();
            $startTime = $schedule->getStartTime();
            $trainingPlan = $this->trainingPlanRepository->findById($planId);
            $users = $this->userTrainingPlanRepository->findByTrainingPlanId($planId);
            $arEmails = array_map(fn($u) => $u->getEmail(), $users) ?? [];
            $trainingDate = $schedule->getDate()->format('d.m.Y');
            $sendAt = $startTime->modify('-1 hour');

            $event = new TrainingNotificationEvent(
                $arEmails,
                $trainingPlan->getName(),
                $trainingPlan->getDescription(),
                $schedule->getId(),
                $sendAt
            );

            $this->eventPublisher->publish($event);
        }
    }
}