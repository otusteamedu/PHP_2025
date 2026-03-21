<?php

namespace Ak\Hw\Domain\Messaging;

interface QueueProducerInterface {
    public function publish(string $queueName,array $messageBody ):void;
}