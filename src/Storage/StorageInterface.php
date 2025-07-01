<?php

namespace App\Storage;

interface StorageInterface
{
	public function addEvent(array $event): void;
	public function clearEvents(): void;
	public function findBestMatch(array $params): ?array;
}