<?php
namespace App\Repository;

interface EventRepositoryInterface {
    public function addEvent(array $event): void;
    public function clearEvents(): void;
    public function findMatchingEvent(array $params): ?array;
}