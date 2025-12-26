<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Domain\Logger;

use Dinargab\Homework20\Domain\Job\Entity\Job;

interface LoggerInterface
{
    public function log(Job $job): void;
}