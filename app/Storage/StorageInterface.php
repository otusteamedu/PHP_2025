<?php

namespace EManager\Storage;

interface StorageInterface
{
    public function addEvent(array $event): void;
    public function clearEvents(): void;
    public function findMatchingEvent(array $matching): ?array;
}