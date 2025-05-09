<?php

declare(strict_types=1);

namespace App\Shared\Application\Subscriber;

use App\Shared\Domain\Event\EventInterface;

interface SubscriberInterface
{
    public function __invoke(EventInterface $event): void;

    public function supportsEvent(EventInterface $event): bool;
}
