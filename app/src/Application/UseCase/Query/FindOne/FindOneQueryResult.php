<?php
declare(strict_types=1);


namespace App\Application\UseCase\Query\FindOne;

use App\Application\DTO\EventDTO;

class FindOneQueryResult
{
    public function __construct(public ?EventDTO $event)
    {
    }

}