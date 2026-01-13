<?php

namespace Arlex2305k\Redis\Storage;

interface StorageInterface
{
	public function storeEvent(int $priority, array $conditions, array $eventData): void;

	public function clearEvents(): void;

	public function findMatches(array $params): ?array;

	public function getAllEvents(): array;
}
