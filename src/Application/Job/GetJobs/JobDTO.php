<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Application\Job\GetJobs;

class JobDTO implements \JsonSerializable
{
    public function __construct(
        public int $id,
        public string $status,
    )
    {

    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
        ];
    }
}