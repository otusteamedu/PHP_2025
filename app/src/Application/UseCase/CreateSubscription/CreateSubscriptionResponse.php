<?php

declare(strict_types=1);

namespace App\Application\UseCase\CreateSubscription;

readonly class CreateSubscriptionResponse
{
    public function __construct(public string $message)
    {
    }
}
