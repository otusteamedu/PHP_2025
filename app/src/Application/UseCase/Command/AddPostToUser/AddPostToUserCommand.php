<?php
declare(strict_types=1);


namespace App\Application\UseCase\Command\AddPostToUser;

use App\Application\Command\CommandInterface;

class AddPostToUserCommand implements CommandInterface
{
    public function __construct(public string $userId, public string $title, public string $content)
    {
    }

}