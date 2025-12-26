<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Application\Statement\Factory;

use Dinargab\Homework20\Domain\Job\Entity\Job;
use Dinargab\Homework20\Domain\Statement\Entity\BankStatement;
use Dinargab\Homework20\Domain\Statement\Factory\BankStatementFactoryInterface;
use Dinargab\Homework20\Domain\ValueObject\DateValueObject;
use Dinargab\Homework20\Domain\ValueObject\Url;

class BankStatementFactory implements BankStatementFactoryInterface
{

    public function create(string $dateFrom, string $dateTo, string $url): BankStatement
    {
        return new BankStatement(new DateValueObject($dateFrom), new DateValueObject($dateTo), new Url($url));
    }

    public function createFromJob(Job $job, string $url): BankStatement
    {
        return new BankStatement($job->getJobParameters()->getDateFrom(), $job->getJobParameters()->getDateTo(), new Url($url));
    }
}