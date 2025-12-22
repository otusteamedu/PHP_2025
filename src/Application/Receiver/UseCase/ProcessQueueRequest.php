<?php
declare(strict_types=1);

namespace Dinargab\Homework19\Application\Receiver\UseCase;

class ProcessQueueRequest
{
    public function __construct(
        public string $queueName,
    )
    {

    }
}