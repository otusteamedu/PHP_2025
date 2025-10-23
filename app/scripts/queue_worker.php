<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Repositories\JobRepository;
use App\Services\RedisQueueService;
use App\Jobs\ProcessRequestJob;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$db = new PDO(
    "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_DATABASE']}",
    $_ENV['DB_USERNAME'],
    $_ENV['DB_PASSWORD']
);

$jobRepository = new JobRepository($db);
$queueService = new RedisQueueService();

echo "Queue worker started...\n";
echo "Listening queue: default\n";
echo "Check interval: 1 second\n\n";

while (true) {
    $jobData = $queueService->pop('default');
    
    if ($jobData) {
        $payload = $jobData['payload'];
        
        echo "Processing job: {$payload['job_id']}\n";
        
        if ($payload['job_class'] === ProcessRequestJob::class) {
            $job = new ProcessRequestJob($payload['job_id'], $payload['data']);
            $job->handle($jobRepository);
            echo "Completed job: {$payload['job_id']}\n\n";
        }
    } else {
        echo "No jobs in queue...\n";
    }
    
    sleep(1);
}