<?php

namespace Restaurant\Application\Events;

use Restaurant\Domain\Interfaces\CookingEventInterface;
use Restaurant\Domain\Interfaces\CookingEventFactoryInterface;
use Restaurant\Application\Events\PreCookingCheckEvent;
use Restaurant\Application\Events\PostCookingCheckEvent;

class CookingEventFactory implements CookingEventFactoryInterface
{
    public function __construct(
        private readonly float $qualityThreshold = 70.0
    ) {
    }

    public function createPreCookingEvent(): CookingEventInterface
    {
        return new PreCookingCheckEvent();
    }

    public function createPostCookingEvent(): CookingEventInterface
    {
        return new PostCookingCheckEvent($this->qualityThreshold);
    }
}
