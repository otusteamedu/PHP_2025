<?php

declare(strict_types=1);

namespace App\Application\EventListener;

interface EventListenerInterface
{
    public function handle(object $event): void;

    /**
     * @return string[]
     */
    public function getSubscribedEvents(): array;
}
