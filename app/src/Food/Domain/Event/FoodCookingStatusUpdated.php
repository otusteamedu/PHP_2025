<?php
declare(strict_types=1);


namespace App\Food\Domain\Event;

use App\Food\Domain\Aggregate\FoodCookingStatusType;
use App\Shared\Domain\Event\EventInterface;

class FoodCookingStatusUpdated implements EventInterface
{
    public function __construct(public string $foodId, public FoodCookingStatusType $currentStatus)
    {
    }

}