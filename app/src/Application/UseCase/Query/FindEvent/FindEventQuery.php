<?php
declare(strict_types=1);


namespace App\Application\UseCase\Query\FindEvent;

use App\Application\Query\QueryInterface;

class FindEventQuery implements QueryInterface
{
    public function __construct(public string $eventId)
    {
    }

}