<?php

namespace App\Domain\Notifier;

interface SubscriberInterface
{
    public function update(array $context = []): void;
}