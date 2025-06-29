<?php

namespace App\Services;

interface ServiceInterface
{
    public function add(string $event, int $priority, array $conditions): string;
    public function answer(array $parameters): string;
    public function getEvents(): string;
    public function clear(): bool;
}