<?php

declare(strict_types=1);

namespace App\Presentation\Console\Command;

use App\Application\UseCase\GenerateTrainingNotificationEvents;

class GenerateTrainingNotificationsCommand
{
    private GenerateTrainingNotificationEvents $useCase;

    public function __construct(GenerateTrainingNotificationEvents $useCase)
    {
        $this->useCase = $useCase;
    }

    public function __invoke(): void
    {
        try {
            $this->useCase->execute(new \DateTimeImmutable('today'));
        } catch (\Exception $e) {
            echo "Error generating training notification events: " . $e->getMessage() . "\n";
            exit(1);
        }

        echo "Training notification events generated successfully.\n";
    }
}
