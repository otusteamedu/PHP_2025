<?php

namespace App\Storage;

class EventStorage
{
	private StorageInterface $storage;

	public function __construct() {
		$type = $_ENV['STORAGE'] ?: 'redis';
		$this->storage = ($type === 'redis') ? new RedisStorage() : new MemcachedStorage();
	}

	public function addEvent(array $event): void {
		$this->storage->addEvent($event);
	}

	public function clearEvents(): void {
		$this->storage->clearEvents();
	}

	public function findBestMatch(array $params): ?array {
		return $this->storage->findBestMatch($params);
	}
}