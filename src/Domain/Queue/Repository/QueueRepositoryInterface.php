<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Domain\Queue\Repository;

use Dinargab\Homework19\Domain\Request\Entity\ReportRequest;

interface QueueRepositoryInterface
{
    public function push(ReportRequest $data): void;

    public function pull(callable $callable): void;
}