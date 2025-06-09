<?php

declare(strict_types=1);

namespace App\Application\UseCase\CreateSubscription;

readonly class CreateSubscriptionRequest
{
    public function __construct(
        public string $category,
        public string $email,
    )
    {
    }
}
