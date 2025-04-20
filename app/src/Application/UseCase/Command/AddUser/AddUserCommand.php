<?php
declare(strict_types=1);


namespace App\Application\UseCase\Command\AddUser;

use App\Application\Command\CommandInterface;

class AddUserCommand implements CommandInterface
{
    public function __construct(public string $email, public string $name)
    {
    }

}