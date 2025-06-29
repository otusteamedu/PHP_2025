<?php

declare(strict_types=1);

namespace App\Application\UseCase\CreateSubscription;

use OpenApi\Attributes as OA;

readonly class CreateSubscriptionResponse
{
    public function __construct(
        #[OA\Property(example: "You have successfully subscribed to the category «Sport».")]
        public string $message
    )
    {
    }
}
