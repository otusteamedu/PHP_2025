<?php
declare(strict_types=1);


namespace App\Application\DTO;

use App\Domain\Entity\Event;

class EventDTOTransformer
{
    public function fromEntity(Event $event): EventDTO
    {
        $dto = new EventDTO();
        $dto->id = $event->getId();
        $dto->name = $event->getName();
        $dto->priority = $event->getPriority();
        $dto->conditions = $event->getConditions();

        return $dto;
    }

}