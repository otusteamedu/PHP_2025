<?php

declare(strict_types=1);

namespace App\Application\UseCase\CreateSubscription;

use OpenApi\Attributes as OA;

readonly class CreateSubscriptionRequest
{
    public function __construct(
        #[OA\Property(example: "Sport")]
        public string $category,
        #[OA\Property(example: "test@test.local")]
        public string $email,
    )
    {
    }
}
