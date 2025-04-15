<?php
declare(strict_types=1);


namespace App\Application\UseCase\Command\RemoveEvent;

use App\Application\Command\CommandInterface;

class RemoveEventCommand implements CommandInterface
{
    public function __construct(public string $eventId)
    {
    }

}