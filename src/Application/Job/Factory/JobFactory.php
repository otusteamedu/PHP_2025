<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Application\Job\Factory;

use Dinargab\Homework20\Domain\Job\Entity\Job;
use Dinargab\Homework20\Domain\Job\Factory\JobFactoryInterface;
use Dinargab\Homework20\Domain\Job\JobStatusEnum;
use Dinargab\Homework20\Domain\ValueObject\DateValueObject;
use Dinargab\Homework20\Domain\ValueObject\JobParameters;

class JobFactory implements JobFactoryInterface
{
    public function create(string $dateFrom, string $dateTo): Job
    {
        $jobParams = new JobParameters(new DateValueObject($dateFrom), new DateValueObject($dateTo));
        $job = new Job($jobParams);
        return $job->setStatus(JobStatusEnum::NEW);
    }

    public function createFromParameters(JobParameters $parameters): Job
    {
        return new Job($parameters);
    }
}