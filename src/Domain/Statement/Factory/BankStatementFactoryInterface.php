<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Domain\Statement\Factory;

use Dinargab\Homework20\Domain\Job\Entity\Job;
use Dinargab\Homework20\Domain\Statement\Entity\BankStatement;

interface BankStatementFactoryInterface
{
    public function create(string $dateFrom, string $dateTo, string $url): BankStatement;
    public function createFromJob(Job $job, string $url): BankStatement;
}