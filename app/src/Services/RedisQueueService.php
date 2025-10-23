<?php

namespace App\Services;

use App\Interfaces\QueueServiceInterface;
use Redis;

class RedisQueueService implements QueueServiceInterface
{
    private Redis $redis;

    public function __construct()
    {
        $this->redis = new Redis();
        $this->redis->connect(
            getenv('REDIS_HOST') ?: 'redis',
            getenv('REDIS_PORT') ?: 6379
        );
    }

    public function push(string $queue, array $job): string
    {
        $jobId = uniqid('queue_', true);
        $jobData = json_encode([
            'id' => $jobId,
            'payload' => $job,
            'created_at' => time()
        ]);

        $this->redis->lPush($queue, $jobData);
        return $jobId;
    }

    public function pop(string $queue): ?array
    {
        $jobData = $this->redis->rPop($queue);
        return $jobData ? json_decode($jobData, true) : null;
    }
}