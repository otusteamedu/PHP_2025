<?php

namespace Restaurant\Domain\Interfaces;

interface CookingEventFactoryInterface
{
    public function createPreCookingEvent(): CookingEventInterface;

    public function createPostCookingEvent(): CookingEventInterface;
}
