<?php
declare(strict_types=1);


namespace App\Application\UseCase\Query\FindOne;

use App\Application\Query\QueryInterface;

class FindOneQuery implements QueryInterface
{
    public function __construct(public string $eventId)
    {
    }

}