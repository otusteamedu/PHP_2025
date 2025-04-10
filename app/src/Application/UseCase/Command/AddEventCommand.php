<?php
declare(strict_types=1);


namespace App\Application\UseCase\Command;

use App\Application\Command\CommandInterface;

class AddEventCommand implements CommandInterface
{
    public function __construct(public int $priority, public string $name, public array $conditions)
    {
    }

}