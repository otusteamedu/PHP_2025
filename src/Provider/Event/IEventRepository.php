<?php

namespace App\Provider\Event;


use App\Entity\Analytics\Event;

interface IEventRepository
{

    public function add(Event $event): bool;

    public function delete(string $eventName): bool;

    public function findEventByConditions(array $conditions): ?string;

    public function getConditionByEvent(string $event): ?array;

    public function deleteAll(): bool;

}
