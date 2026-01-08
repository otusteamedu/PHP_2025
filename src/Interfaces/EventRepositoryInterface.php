<?php

namespace App\Interfaces;

interface EventRepositoryInterface {
    public function addEvent(array $event);
    public function clearAll();
    public function getAllEvents(): array;
}