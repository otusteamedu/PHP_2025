<?php
declare(strict_types=1);


namespace App\Domain\Repository;

use App\Domain\Entity\Event;

interface EventRepositoryInterface
{
    public function add(Event $event): void;

}