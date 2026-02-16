<?php

namespace App\Application\Observer;

interface CookingObserverInterface
{
    public function update(CookingStatusChangedEvent $event): void;
}
