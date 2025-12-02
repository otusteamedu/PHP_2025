<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Domain\Job\Entity;

use Dinargab\Homework20\Domain\Job\JobStatusEnum;
use Dinargab\Homework20\Domain\ValueObject\JobParameters;
use JsonSerializable;

class Job implements JsonSerializable
{
    private int $id;

    private ?JobStatusEnum $status;

    private ?int $bankStatementId = null;

    public function __construct(
        private readonly JobParameters $jobParameters
    )
    {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getStatus(): JobStatusEnum
    {
        return $this->status;
    }

    public function setStatus(JobStatusEnum $status): Job
    {
        $this->status = $status;
        return $this;
    }

    public function getJobParameters(): JobParameters
    {
        return $this->jobParameters;
    }

    public function getBankStatementId(): ?int
    {
        return $this->bankStatementId;
    }

    public function setBankStatementId(?int $statementId): void
    {
        $this->bankStatementId = $statementId;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'statement_id' => $this->bankStatementId,
        ];
    }
}