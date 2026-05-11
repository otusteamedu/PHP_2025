<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\TrainingNotificationEvent;
use App\Domain\Event\EventPublisherInterface;
use App\Domain\Repository\TrainingPlanRepositoryInterface;
use App\Domain\Repository\TrainingScheduleRepositoryInterface;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Repository\UserTrainingPlanRepositoryInterface;

readonly class GenerateTrainingNotificationEvents
{
    public function __construct(
        private TrainingScheduleRepositoryInterface $trainingScheduleRepository,
        private UserTrainingPlanRepositoryInterface $userTrainingPlanRepository,
        private EventPublisherInterface             $eventPublisher,
        private TrainingPlanRepositoryInterface     $trainingPlanRepository,
        private UserRepositoryInterface             $userRepository
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

            $dayTrainingPlans = $this->trainingPlanRepository->findById($schedule->getTrainingPlanId());
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

//            pr([$event, json_encode($event)], true, true);

            $this->eventPublisher->publish($event);



           /* $trainingPlan = $this->trainingPlanRepository->findById($userPlan->getTrainingPlanId());
            $user = $this->userRepository->findById($userPlan->getUserId());

            if ($trainingPlan === null || $user === null) {
                // Пропускаем, если не найден план или пользователь
                continue;
            }







            $event = new TrainingNotificationEvent(
                $userPlan->getUserId(),
                $userPlan->getTrainingPlanId(),
                $schedule->getId(),
                $message,
                $sendAt
            );

            pr([$event], true, true);

            $this->eventPublisher->publish($event);*/
        }
    }
}
