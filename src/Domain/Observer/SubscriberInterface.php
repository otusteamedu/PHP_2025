<?php

declare(strict_types=1);

namespace App\Domain\Observer;

interface SubscriberInterface
{
    public function update(EventInterface $event): void;
}
