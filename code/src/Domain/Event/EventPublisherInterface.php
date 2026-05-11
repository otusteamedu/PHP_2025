<?php

declare(strict_types=1);

namespace App\Domain\Event;

interface EventPublisherInterface
{
    public function publish(object $event): void;
}
