<?php
namespace App\Workers;

use App\Interfaces\JobRepositoryInterface;
use App\Interfaces\QueueServiceInterface;

class QueueWorker
{
    public function __construct(
        private JobRepositoryInterface $jobRepository,
        private QueueServiceInterface $queueService
    ) {}

    public function start(string $queueName = 'default'): void
    {
        echo "Starting worker for queue: {$queueName}\n";

        while (true) {
            $jobData = $this->queueService->pop($queueName);
            
            if ($jobData) {
                $this->processJob($jobData);
            }
            
            sleep(1);
        }
    }

    private function processJob(array $jobData): void
    {
        // Логика обработки разных типов jobs
        $payload = $jobData['payload'];
        $jobClass = $payload['job_class'];
        
        if (class_exists($jobClass)) {
            $job = new $jobClass($payload['job_id'], $payload['data']);
            $job->handle($this->jobRepository);
        }
    }
}