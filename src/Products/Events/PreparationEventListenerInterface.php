<?php declare(strict_types=1);

namespace Fastfood\Products\Events;

interface PreparationEventListenerInterface
{
    public function onPrePreparation(PrePreparationEvent $event): void;
    public function onPostPreparation(PostPreparationEvent $event): void;
}