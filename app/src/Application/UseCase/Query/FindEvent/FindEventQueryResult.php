<?php
declare(strict_types=1);


namespace App\Application\UseCase\Query\FindEvent;

use App\Application\DTO\EventDTO;

class FindEventQueryResult
{
    public function __construct(public ?EventDTO $event)
    {
    }

}