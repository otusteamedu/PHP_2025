<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Domain\Job\Factory;

use Dinargab\Homework20\Domain\Job\Entity\Job;
use Dinargab\Homework20\Domain\ValueObject\JobParameters;

interface JobFactoryInterface
{
    public function create(string $dateFrom, string $dateTo): Job;

    public function createFromParameters(JobParameters $parameters): Job;
}