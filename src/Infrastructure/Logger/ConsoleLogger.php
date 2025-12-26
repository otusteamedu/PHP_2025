<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Infrastructure\Logger;

use Dinargab\Homework20\Domain\Job\Entity\Job;
use Dinargab\Homework20\Domain\Logger\LoggerInterface;

class ConsoleLogger implements LoggerInterface
{

    public function log(Job $job): void
    {
        echo "Job: {$job->getId()} has been processed" . PHP_EOL;
    }
}